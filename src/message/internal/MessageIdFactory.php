<?php

namespace Orlion\CatAgentPhp\Message\Internal;

class MessageIdFactory
{
    private static $instance;

    private function __construct()
    {
        
    }

    public static function getInstance(): MessageIdFactory
    {
        if (is_null(self::$instance)) {
            self::$instance = new MessageIdFactory();
        }

        return self::$instance;
    }

    public function getNextId(string $domain = ''): string
    {
        return '';
    }
}