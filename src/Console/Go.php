<?php

namespace Somos\Console;

final class Go
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
