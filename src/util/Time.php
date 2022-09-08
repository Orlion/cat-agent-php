<?php

namespace Orlion\CatAgentPhp\Util;

class Time
{
    public static function currentTimeMillis(): float
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    public static function currentTimeMicro(): float
    {
        list($t1, $t2) = explode(' ', microtime());
        return (floatval($t1) + floatval($t2));
    }
}