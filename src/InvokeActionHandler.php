<?php

namespace Somos;

use DI\Container;

final class InvokeActionHandler
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handles the given message.
     *
     * @param InvokeAction $message
     *
     * @return void
     */
    public function __invoke(InvokeAction $message)
    {
        $this->respond(
            $message,
            $message->action !== null ? $this->invoke($message) : null
        );
    }

    /**
     * @param InvokeAction $message
     *
     * @return mixed|null
     */
    private function invoke(InvokeAction $message)
    {
        return $message->action->getHandler() !== null
            ? $this->container->call($message->action->getHandler(), $message->parameters)
            : null;
    }

    /**
     * @param InvokeAction $message
     * @param mixed        $actionResult
     *
     * @return void
     */
    private function respond(InvokeAction $message, $actionResult)
    {
        if ($message->action->getResponder() === null) {
            return;
        }

        $this->container->call(
            $message->action->getResponder(),
            $actionResult ? ['data' => $actionResult] : []
        );
    }
}
