<?php

namespace Somos;

class Action
{
    private $matcher = '';

    private $handler = null;

    private $responder = null;

    public function __construct($matcher)
    {
        $this->matcher  = $matcher;
    }

    /**
     * @param mixed $matcher
     *
     * @return Action
     */
    public static function matches($matcher)
    {
        return new static($matcher);
    }

    public function uses($callable)
    {
        $this->handler = $callable;
    }

    public function responds($callable)
    {
        $this->responder = $callable;

        return $this;
    }

    public function getMatcher()
    {
        return $this->matcher;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getResponder()
    {
        return $this->responder;
    }
}