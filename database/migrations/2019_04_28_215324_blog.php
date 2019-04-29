<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Blog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->string('title');
            $blueprint->text('body');
            $blueprint->integer('author_id')->nullable();
            $blueprint->integer('views_counter')->default(0);
            $blueprint->timestamps();

            $blueprint->foreign(['author_id'], 'blog_records__author__fk')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });

        Schema::create('file', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->string('path');
        });

        Schema::create('blog_files', function(Blueprint $blueprint){
            $blueprint->integer('blog_id');
            $blueprint->integer('file_id');

            $blueprint->foreign('blog_id', 'blog_files__blog__fk')->references('id')->on('blog')->onUpdate('CASCADE')->onDelete('CASCADE');
            $blueprint->foreign('file_id', 'blog_files__file__fk')->references('id')->on('file')->onUpdate('CASCADE')->onDelete('CASCADE');

            $blueprint->primary(['blog_id', 'file_id']);
            $blueprint->index('blog_id');
            $blueprint->index('file_id');
        });

        Schema::create('tag_catalog', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->string('term');
        });

        Schema::create('blog_tags', function (Blueprint $blueprint) {
            $blueprint->integer('blog_id');
            $blueprint->integer('tag_id');

            $blueprint->foreign('blog_id', 'blog_tags__blog__fk')->references('id')->on('blog')->onUpdate('CASCADE')->onDelete('CASCADE');
            $blueprint->foreign('tag_id', 'blog_tags__tag__fk')->references('id')->on('tag_catalog')->onUpdate('CASCADE')->onDelete('CASCADE');

            $blueprint->primary(['blog_id', 'tag_id']);
            $blueprint->index('blog_id');
            $blueprint->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
