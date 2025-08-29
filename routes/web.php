<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ProjectController;
use App\Middleware\AdminMiddleware;

$router = new Router();

// Auth routes
$router->addRoute('POST', '/api/v1/auth/register', 'AuthController@register');
$router->addRoute('POST', '/api/v1/auth/login', 'AuthController@login');
$router->addRoute('POST', '/api/v1/auth/logout', 'AuthController@logout');
$router->addRoute('GET', '/api/v1/auth/me', 'AuthController@me');
$router->addRoute('GET', '/api/v1/auth/profile', 'AuthController@updateProfile');
$router->addRoute('PUT', '/api/v1/auth/profile', 'AuthController@updateProfile');
$router->addRoute('PUT', '/api/v1/auth/password', 'AuthController@changePassword');
$router->addRoute('POST', '/api/v1/auth/avatar', 'AuthController@uploadAvatar');

// Projects routes (example)
$router->addRoute('GET', '/api/v1/projects', 'ProjectController@index');
$router->addRoute('GET', '/api/v1/projects/:id', 'ProjectController@show');
$router->addRoute('POST', '/api/v1/projects', 'ProjectController@store');
$router->addRoute('PUT', '/api/v1/projects/:id', 'ProjectController@update');
$router->addRoute('DELETE', '/api/v1/projects/:id', 'ProjectController@delete');

// Donations routes
$router->addRoute('GET', '/api/v1/donations', 'DonationController@index', [AdminMiddleware::class]);
$router->addRoute('GET', '/api/v1/donations/:id', 'DonationController@show', [AdminMiddleware::class]);
$router->addRoute('POST', '/api/v1/donations', 'DonationController@create');
$router->addRoute('PUT', '/api/v1/donations/:id/status', 'DonationController@updateStatus', [AdminMiddleware::class]);
$router->addRoute('GET', '/api/v1/donations/stats', 'DonationController@stats', [AdminMiddleware::class]);
$router->addRoute('GET', '/api/v1/projects/:id/donations-total', 'DonationController@projectTotal');

// Reports routes
$router->addRoute('GET', '/api/v1/admin/reports/donations', 'ReportsController@getDonationsReport', [AdminMiddleware::class]);
$router->addRoute('GET', '/api/v1/admin/reports/projects', 'ReportsController@getProjectsReport', [AdminMiddleware::class]);
$router->addRoute('GET', '/api/v1/admin/reports/users', 'ReportsController@getUsersReport', [AdminMiddleware::class]);
$router->addRoute('GET', '/api/v1/admin/reports/export/:type', 'ReportsController@exportReport', [AdminMiddleware::class]);
$router->addRoute('GET', '/api/v1/admin/reports/stats', 'ReportsController@getDashboardStats', [AdminMiddleware::class]);

$router->addRoute('GET', '/api/health', function() {
    echo json_encode(['status' => 'OK', 'message' => 'Server is running']);
});

return $router;