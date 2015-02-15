<?php

namespace Somos;

use DI\Container;
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;

final class InvokeActionHandler implements MessageHandler
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
     * @param InvokeAction|Message $message
     *
     * @return void
     */
    public function handle(Message $message)
    {
        if ($message instanceof InvokeAction === false) {
            throw new \InvalidArgumentException(
                'The handler responsible for the Console\'s Run message expects a message of class Somos\InvokeAction, '
                . 'an object of class "' . get_class($message) . '" was received'
            );
        }

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