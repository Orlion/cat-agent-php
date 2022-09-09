<?php

namespace Orlion\CatAgentPhp\Message\Internal;

class DefaultMessageManager
{
    private static $instance;

    public static function getInstance(): DefaultMessageManager
    {
        if (is_null(self::$instance)) {
            self::$instance = new DefaultMessageManager();
        }
        
        return self::$instance;
    }
}