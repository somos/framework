<?php

namespace Somos;

use League\Tactician\CommandBus;

final class Actions implements \ArrayAccess, \IteratorAggregate
{
    /** @var Action[] */
    private $actions = [];

    /** @var CommandBus */
    private $messageBus;

    public function __construct(Commandbus $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function offsetSet($index, $newval)
    {
        if ($newval instanceof Action == false) {
            throw new \InvalidArgumentException(
                'Only actions may be added to the Actions collection, received: ' . get_class($newval)
            );
        }

        if (empty($index)) {
            $this->actions[] = $newval;
        } else {
            $this->actions[$index] = $newval;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->actions[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->actions[$offset] : null;
    }

    public function offsetUnset($offset)
    {
        unset($this->actions[$offset]);
    }

    public function handle($index, array $parameters = [])
    {
        $action = $this[$index];
        if ($action === null) {
            return;
        }

        $this->messageBus->handle(new InvokeAction($action, $parameters));
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->actions);
    }
}
