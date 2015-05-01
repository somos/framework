<?php

namespace Somos\Http;

use Somos\Actions;
use Somos\Http\Routing\Route;
use Phly\Http\ServerRequest;
use Somos\Http\Routing\Collection;

final class GoHandler
{
    /** @var ServerRequest */
    private $request;

    /** @var Actions */
    private $actions;

    public function __construct(Actions $actions, ServerRequest $request)
    {
        $this->request = $request;
        $this->actions = $actions;
    }

    public function __invoke(Go $message)
    {
        $actions = $this->actions;
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) use ($actions) {
            foreach ($actions as $action) {
                if ($action->getMatcher() instanceof Route == false) {
                    continue;
                }

                $route = $action->getMatcher();
                $r->addRoute(
                    $route->method,
                    $route->uriTemplate,
                    function ($variables = []) use ($action) {
                        // TODO: Extract (post) variables from request and insert into action some way easy to consume
                        // URI Variables = parameter
                        // Get Variables = parameter
                        // POST Variables = entity object added to parameters
                        $action->handle($variables);
                    }
                );
            }
        });

        $uri    = (string)$this->request->getUri()->getPath();
        $method = $this->request->getMethod();

        $routeInfo = $dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                // TODO: Use a more specialized exception
                throw new \Exception(
                    'No action could be found to deal with uri "' . $uri . '" and method "' . $method . '"'
                );
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // TODO: Use a more specialized exception
                throw new \Exception(
                    'The method "' . $method . '" is not allowed with uri "' . $uri . '". The following are allowed '
                    . 'methods: ' . implode($allowedMethods)
                );
            case \FastRoute\Dispatcher::FOUND:
                list(,$handler, $vars) = $routeInfo;
                $handler($vars);
                break;
        }
    }
}
