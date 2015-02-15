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
        if ($message->action === null) {
            return;
        }

        $actionResult = $message->action->getHandler() !== null
            ? $this->container->call($message->action->getHandler(), $message->parameters)
            : null;

        if ($message->action->getResponder() !== null) {
            $responderData = [];
            if ($actionResult) {
                $responderData = ['data' => $actionResult];
            }

            $this->container->call($message->action->getResponder(), $responderData);
        }
    }
}