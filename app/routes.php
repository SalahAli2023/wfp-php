<?php
use App\Core\Router;

$router = new Router();

// Auth routes
$router->addRoute('POST', '/api/v1/auth/register', 'AuthController@register');
$router->addRoute('POST', '/api/v1/auth/login', 'AuthController@login');
$router->addRoute('POST', '/api/v1/auth/logout', 'AuthController@logout');
$router->addRoute('GET', '/api/v1/auth/me', 'AuthController@me');

// Projects routes (example)
$router->addRoute('GET', '/api/v1/projects', 'ProjectController@index');
$router->addRoute('GET', '/api/v1/projects/:id', 'ProjectController@show');
$router->addRoute('POST', '/api/v1/projects', 'ProjectController@store');
$router->addRoute('PUT', '/api/v1/projects/:id', 'ProjectController@update');
$router->addRoute('DELETE', '/api/v1/projects/:id', 'ProjectController@delete');
