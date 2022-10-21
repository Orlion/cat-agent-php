<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\MessageManager;

/**
 * DefaultEvent
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Internal
 */
class DefaultEvent extends AbstractMessage implements Event
{
    /**
     * @var MessageManager
     */
    private $manager;

    /**
     * @param string $type
     * @param string $name
     * @param MessageManager $manager
     */
    public function __construct(string $type, string $name, MessageManager $manager)
    {
        parent::__construct($type, $name);

        $this->manager = $manager;
    }

    /**
     * @return void
     */
    public function complete(): void
    {
        $this->setComplete(true);

        $this->manager->add($this);
    }
}