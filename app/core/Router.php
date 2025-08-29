<?php
namespace App\Core;
use App\Core\Response;
class Router {
    private $routes = [];
    private $params = [];
    private Response $response;

    public function __construct() {
        $this->response = new Response();
    }

    public function addRoute(
        string $method, 
        string $path, 
        $handler, 
        array $middleware = []
    ): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(string $uri, string $method): void {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($this->match($route['path'], $uri) && 
                $route['method'] === strtoupper($method)) {
                
                // Execute middleware
                if (!$this->executeMiddleware($route['middleware'])) {
                    return;
                }

                $this->executeHandler($route['handler']);
                return;
            }
        }

        $this->response->notFound();
    }

    private function executeMiddleware(array $middleware): bool {
        foreach ($middleware as $middlewareClass) {
            $middlewareInstance = new $middlewareClass();
            if (!$middlewareInstance->handle()) {
                return false;
            }
        }
        return true;
    }

    private function executeHandler($handler): void {
        if (is_callable($handler)) {
                call_user_func_array($handler, array_values($this->params));
        } elseif (is_string($handler)) {
            $this->callController($handler, $this->params);
        }
    }

    private function match($routePath, $requestUri) {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', $requestUri);

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        $this->params = [];
        foreach ($routeParts as $index => $part) {
            if (strpos($part, ':') === 0) {
                $paramName = substr($part, 1);
                $this->params[$paramName] = $requestParts[$index];
            } elseif ($part !== $requestParts[$index]) {
                return false;
            }
        }
        return true;
    }

    private function callController(string $handler, array $params = []): void {
        list($controller, $action) = explode('@', $handler);
        $controller = "App\\Controllers\\" . $controller;
        
        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $action)) {
                call_user_func_array([$controllerInstance, $action], array_values($params));
                return;
            }
        }
        $this->response->notFound();
    }
}