<?php

namespace Somos\Module;

use Somos\Module as SomosModule;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use Somos\MessageBus\LazyLoadingPhpDiMessageHandlerMap;

final class MessageBus implements SomosModule
{
    public function __invoke()
    {
        return [
            'command.middlewares' => [
                \DI\link(CommandHandlerMiddleware::class)
            ],

            CommandBus::class           => \DI\object()->constructor(\DI\link('command.middlewares')),
            CommandNameExtractor::class => \DI\object(ClassNameExtractor::class),
            HandlerLocator::class       => \DI\object(LazyLoadingPhpDiMessageHandlerMap::class),
            MethodNameInflector::class  => \DI\object(InvokeInflector::class),
        ];
    }
}
