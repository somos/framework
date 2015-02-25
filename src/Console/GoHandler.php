<?php

namespace Somos\Console;

use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;
use Somos\Actions;
use Symfony\Component\Console\Application;

final class GoHandler implements MessageHandler
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
        if ($message instanceof Go === false) {
            throw new \InvalidArgumentException(
                'The handler responsible for the Console\'s Run message expects a message of class Somos\Console\Run, '
                . 'an object of class "' . get_class($message) . '" was received'
            );
        }

        $this->setNameAndVersion($message);
        $this->registerConsoleCommands();
        $this->console->run();
    }

    /**
     * @param Run $message
     */
    private function setNameAndVersion(Go $message)
    {
        $this->console->setName($message->title);
        $this->console->setVersion($message->version);
    }

    private function registerConsoleCommands()
    {
        foreach ($this->actions as $index => $action) {
            if ($this->isConsoleCommand($action) === false) {
                continue;
            }

            $this->registerCommandWithConsole($action->getMatcher(), $index);
        }
    }

    /**
     * @param \Somos\Action $action
     * @return bool
     */
    private function isConsoleCommand(\Somos\Action $action)
    {
        return $action->getMatcher() instanceof Command;
    }

    /**
     * @param Command $command
     * @param $index
     */
    private function registerCommandWithConsole(Command $command, $index)
    {
        $this->console->add($command);

        $command->registerAction(
            function () use ($index) {
                $this->actions->handle($index);
            }
        );
    }
}
