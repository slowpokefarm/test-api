<?php

namespace Tests\Feature;

use App\Blog;
use App\Folksonomy\Folksonomy;
use App\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\InteractsWithFile;
use Tests\InteractsWithUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Support\RefreshFlow;

class BlogTest extends TestCase
{
    use WithFaker;
    use DatabaseMigrations;
    use InteractsWithUser;
    use InteractsWithFile;

    public function testCreateRecord()
    {
        $data = ['title' => $this->faker->text, 'body' => $this->faker->text(5000)];

        $resp = $this->json('POST', '/api/blog', $data);
        $resp->assertStatus(403);

        $user = $this->registerRandomUser($this->faker);
        $user2 = $this->registerRandomUser($this->faker);

        $this->actingAs($user2);
        $this->json('POST', '/api/blog', $data);

        $this->actingAs($user);
        $resp = $this->json('POST', '/api/blog', $data);
        $resp->assertJson(['data' => ['title' => $data['title'], 'body' => $data['body']]]);

        $id = $resp->json('data.id');

        {
            $data2 = ['title' => $this->faker->text, 'body' => $this->faker->text(5000)];
            $this->json('POST', "/api/blog/{$id}", $data2)
                ->assertJson(['data' => $data2]);
        }

        {
            $this->actingAsScoped($user2, function () use ($id, $data2) {
                $this->json('POST', "/api/blog/{$id}", $data2)->assertStatus(403);
            });
        }

        {
            $this->json('GET', "/api/blog/{$id}")->assertStatus(200)->assertJson(['data' => $data2]);
        }
    }

    public function testReadRecord()
    {
        [$user1, $user2] = [$this->registerRandomUser($this->faker), $this->registerRandomUser($this->faker)];

        factory(Blog::class)->times(3)->create()
            ->each(function (Blog $record) use ($user1) {
                $record->author()->associate($user1);
                $record->save();
            });
        factory(Blog::class)->times(7)->create()
            ->each(function (Blog $record) use ($user2) {
                $record->author()->associate($user2);
                $record->save();
            });

        $this->actingAs($user1);

        $this->json('GET', '/api/user/me/blogs')->assertStatus(200)->assertJsonCount(3, 'data');
        $this->json('GET', "/api/user/{$user2->id}/blogs")->assertStatus(200)->assertJsonCount(7, 'data');
    }

    public function testTags()
    {
        $user = $this->registerRandomUser($this->faker);
        $this->be($user);

        /** @var Blog $blog */
        $blog = factory(Blog::class)->create();
        $blog->author()->associate($user);
        $blog->save();

        $this->json('GET', "/api/blog/{$blog->id}/tags")->assertJsonCount(0, 'data');
        $this->json('POST', "/api/blog/{$blog->id}/tags", ['action' => 'attach', 'term' => 'Code'])->assertStatus(200);
        $this->json('POST', "/api/blog/{$blog->id}/tags", ['action' => 'attach', 'term' => 'Shit'])->assertStatus(200);
        $this->json('GET', "/api/blog/{$blog->id}/tags")->assertJsonCount(2, 'data');
        $this->json('POST', "/api/blog/{$blog->id}/tags", ['action' => 'detach', 'term' => 'Code'])->assertStatus(200);
        $this->json('GET', "/api/blog/{$blog->id}/tags")->assertJsonCount(1, 'data');
    }

    public function testFiles()
    {
        $user = $this->registerRandomUser($this->faker);
        $this->be($user);

        /** @var Blog $blog */
        $blog = factory(Blog::class)->create();
        $blog->author()->associate($user);
        $blog->save();

        $files = [
            $this->json('POST', '/api/file', ['content' => 'file content 1'])->json('data.id'),
            $this->json('POST', '/api/file', ['content' => 'file content 2'])->json('data.id')
        ];

        $this->json('POST', "/api/blog/{$blog->id}/files", ['action' => 'attach', 'file_id' => $files])->assertStatus(200);
        $this->json('GET', "/api/blog/{$blog->id}/files")->assertJsonCount(2, 'data');
        $this->json('POST', "/api/blog/{$blog->id}/files", ['action' => 'detach', 'file_id' => $files[0]])->assertStatus(200);
        $this->json('GET', "/api/blog/{$blog->id}/files")->assertJsonCount(1, 'data');
    }

    public function testBlogList()
    {
        /** @var Folksonomy $folksonomy */
        $folksonomy = $this->app->make(Folksonomy::class);

        $user = factory(User::class)->create();
        /** @var Collection $blogs */
        $blogs = factory(Blog::class)->times(10)->create();
        $file = $this->createFile($this->faker);

        $blogs->each(function (Blog $blog) use ($user) {
            $blog->author()->associate($user);
            $blog->save();
        });
        $blogs->take(3)->each(function (Blog $blog) use ($file) {
            $blog->files()->attach($file);
        });
        $blogs->take(7)->each(function (Blog $blog) use ($folksonomy) {
            $blog->tags()->attach($folksonomy->findOrCreate('Code'));
        });
        $blogs->shuffle()->take(5)->each(function (Blog $blog) use ($folksonomy) {
            $blog->tags()->attach($folksonomy->findOrCreate('Video'));
        });

        $this->json('GET', '/api/blog')->assertJsonCount(10, 'data');
        $this->json('GET', '/api/blog?has_file=1')->assertJsonCount(3, 'data');
        $this->json('GET', '/api/blog?tag=Code')->assertJsonCount(7, 'data');
    }

    public function testViews()
    {
        /** @var Blog $blog */
        $blog = factory(Blog::class)->create();
        $blog->refresh(); // sqlite

        $this->assertEquals(0, $blog->views_counter);
        $this->json('POST', "/api/blog/{$blog->id}/view")->assertStatus(200);
        $blog->refresh();
        $this->assertEquals(1, $blog->views_counter);
        $this->withSession(["blog-view-{$blog->id}", true])->json('POST', "/api/blog/{$blog->id}/view")->assertStatus(200); // Нет встроенного механизма для поддержания сессии
        $blog->refresh();
        $this->assertEquals(1, $blog->views_counter);
    }
}
