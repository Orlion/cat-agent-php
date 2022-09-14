<?php

namespace Orlion\CatAgentPhp\Message\Internal;

use Orlion\CatAgentPhp\Message\MessageManager;
use Orlion\CatAgentPhp\Message\Metric;

class DefaultMetric extends AbstractMessage implements Metric
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