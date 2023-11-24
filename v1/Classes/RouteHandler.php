<?php namespace UpdateApi\v1\Classes;

use UpdateApi\v1\Exceptions\RouteException;

class RouteHandler {
    private const ENDPOINTS_NAMESPACE = 'UpdateApi\\v1\\Endpoints\\';

    /**
     * @throws RouteException
     */
    public function processRequest(): string {
        $uriParts = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if (!$uriParts[1]) throw new RouteException('/');

        $routeController = $this->stringToClassNamespace($uriParts[1]);
        if (!class_exists($routeController)) throw new RouteException($uriParts[1]);

        $route = new $routeController();
        return json_encode($route->getResponse());
    }

    private function stringToClassNamespace(string $name = ''): string {
        if (!$name) return '';

        return self::ENDPOINTS_NAMESPACE . ucfirst(strtolower($name));
    }
}