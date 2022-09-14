<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\MessageManager;

class DefaultEvent extends AbstractMessage implements Event
{
    private $manager;

    public function __construct(string $type, string $name, MessageManager $manager)
    {
        parent::__construct($type, $name);

        $this->manager = $manager;
    }

    public function complete(): void
    {
        $this->setComplete(true);

        $this->manager->add($this);
    }
}