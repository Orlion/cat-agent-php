<?php

namespace Orlion\CatAgentPhp\Configuration\Client\Entity;

class ClientConfig
{
    private $enabled = true;

    public function setEnabled(bool $enabled): ClientConfig
    {
        $this->enabled = $enabled;
        return $this;
    }
}
