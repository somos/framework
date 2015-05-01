<?php

namespace Somos;

final class InvokeAction
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
