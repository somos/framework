<?php

namespace Somos\Console;

use SimpleBus\Message\Message;

final class Go implements Message
{
    /** @var string */
    public $title;

    /** @var string */
    public $version;

    public function __construct($title = 'Somos', $version = '1.0.0')
    {
        $this->title = $title;
        $this->version = $version;
    }
}