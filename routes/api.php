<?php

use App\Blog;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Blog\TagsController;
use App\Http\Controllers\Blog\FilesController as BlogFilesController;

/** @var Router $router */
$router = app()->make(Router::class);

$router->group(['middleware' => 'api'], function (Router $router) {
    $router->group(['prefix' => 'auth'], function (Router $router) {
        $router->post('/register', [RegisterController::class, 'register']);
        $router->post('/login', [AuthController::class, 'login']);
        $router->post('/logout', [AuthController::class, 'logout']);
        $router->post('/me', [AuthController::class, 'me']);
        $router->post('/refresh', [AuthController::class, 'refresh']);
    });

    $router->group(['prefix' => 'user'], function (Router $router) {
        $router->group(['prefix' => '{user}'], function (Router $router) {
            $router->get('blogs', [UserController::class, 'userBlogs']);
        });
    });

    $router->group(['prefix' => 'file'], function (Router $router) {
        $router->post('/', [FileController::class, 'upload']);
        $router->delete('/{file}', [FileController::class, 'delete']);
        $router->get('/{file}', [FileController::class, 'get']);
    });

    $router->group(['prefix' => 'blog'], function (Router $router) {
        $router->post('/', [BlogController::class, 'create'])->middleware('can:blog-create');
        $router->get('/', [BlogController::class, 'list']);
        $router->get('/{blog}', [BlogController::class, 'get']);
        $router->get('/{blog}/tags', [TagsController::class, 'list']);
        $router->get('/{blog}/files', [BlogFilesController::class, 'list']);

        $router->group(['middleware' => 'can:blog-update,blog'], function (Router $router) {
            $router->post('/{blog}', [BlogController::class, 'update']);

            $router->post('/{blog}/tags', function (Blog $blog, Request $request, TagsController $tagsController) {
                if ($request->input('action') === 'attach') {
                    $tagsController->attach($blog, $request);
                } else if ($request->input('action') === 'detach') {
                    $tagsController->detach($blog, $request);
                }
            });

            $router->post('/{blog}/files', function (Blog $blog, Request $request, BlogFilesController $filesController) {
                if ($request->input('action') === 'attach') {
                    $filesController->attach($blog, $request);
                } else if ($request->input('action') === 'detach') {
                    $filesController->detach($blog, $request);
                }
            });
        });

        $router->get('/{blog}/files', [BlogFilesController::class, 'get']);
        $router->post('/{blog}/view', [BlogController::class, 'view']);
    });
});
