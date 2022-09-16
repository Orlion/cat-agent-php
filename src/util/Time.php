<?php

namespace Orlion\CatAgentPhp\Util;

class Time
{
    public static function currentTimeMillis(): int
    {
        $mt = explode(' ', microtime());
        return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
    }

    public static function currentTimeMicro(): int
    {
        $mt = explode(' ', microtime());
        return ((int)$mt[1]) * 1000000 + ((int)($mt[0] * 1000000));
    }
}