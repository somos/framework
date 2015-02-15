<?php

namespace Somos\Console;

use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;
use Somos\Actions;
use Symfony\Component\Console\Application;

final class RunHandler implements MessageHandler
{
    /** @var Application */
    private $console;

    /** @var Actions */
    private $actions;

    public function __construct(Application $console, Actions $actions)
    {
        $this->console = $console;
        $this->actions = $actions;
    }

    /**
     * Handles the given message.
     *
     * @param Message|Run $message
     *
     * @throws \InvalidArgumentException if the given object is not of class Run.
     *
     * @return void
     */
    public function handle(Message $message)
    {
        if ($message instanceof Run === false) {
            throw new \InvalidArgumentException(
                'The handler responsible for the Console\'s Run message expects a message of class Somos\Console\Run, '
                . 'an object of class "' . get_class($message) . '" was received'
            );
        }

        $this->console->setName($message->title);
        $this->console->setVersion($message->version);

        foreach ($this->actions as $index => $action) {
            $matcher = $action->getMatcher();
            if ($matcher instanceof Command == false) {
                continue;
            }

            /** @var Command $matcher */
            $this->console->add($matcher);
            $matcher->registerAction(
                function () use ($index) {
                    $this->actions->handle($index);
                }
            );
        }

        $this->console->run();
    }
}