<?php

namespace Somos\MessageBus;

use Assert\Assertion;
use DI\Container;
use League\Tactician\Handler\Locator\HandlerLocator;

final class LazyLoadingPhpDiMessageHandlerMap implements HandlerLocator
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Retrieves the handler for a specified command
     *
     * @param string $commandName
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function getHandlerForCommand($commandName)
    {
        if (class_exists($commandName) == false) {
            throw new \InvalidArgumentException(
                'The provided message name should be an existing class, received "'. $commandName . '"'
            );
        }

        $handlerClassName = $commandName . 'Handler';
        if (class_exists($handlerClassName) == false) {
            throw new \InvalidArgumentException("No handler could be found for $commandName");
        }

        return $this->loadHandlerService($handlerClassName);
    }


    private function loadHandlerService($handlerClassName)
    {
        return $this->container->get($handlerClassName);
    }
}
