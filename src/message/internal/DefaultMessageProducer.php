<?php

namespace Orlion\CatAgentPhp\Message\Internal;

class DefaultMessageProducer
{
    private static $instance;

    public static function getInstance(): DefaultMessageProducer
    {
        if (is_null(self::$instance)) {
            self::$instance = new DefaultMessageProducer();
        }
        
        return self::$instance;
    }
}