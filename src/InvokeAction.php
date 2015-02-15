<?php

namespace Somos;

use SimpleBus\Message\Message;

final class InvokeAction implements Message
{
    /** @var Action */
    public $action;

    /** @var mixed[] */
    public $parameters = [];

    public function __construct(Action $action, array $parameters = [])
    {
        $this->action = $action;
        $this->parameters = $parameters;
    }
}