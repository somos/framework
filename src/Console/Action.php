<?php

namespace Somos\Console;

use Somos\Action as SomosAction;

/**
 * @method Command getMatcher()
 */
class Action extends SomosAction
{
    /**
     * @param string $matcher
     *
     * @return $this
     */
    public static function matching($matcher)
    {
        if (is_string($matcher) != true) {
            throw new \InvalidArgumentException(
                'A console action should have a URI pattern as matching term; received: ' . var_export($matcher, true)
            );
        }

        return parent::matching(new Command($matcher));
    }

    public function describeAs($description)
    {
        $this->getMatcher()->setDescription($description);

        return $this;
    }
}
