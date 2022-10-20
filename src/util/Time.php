<?php

namespace Orlion\CatAgentPhp\Util;

/**
 * Time
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Util
 */
class Time
{
    /**
     * @return int
     */
    public static function currentTimeMillis(): int
    {
        $mt = explode(' ', microtime());
        return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
    }

    /**
     * @return int
     */
    public static function currentTimeMicro(): int
    {
        $mt = explode(' ', microtime());
        return ((int)$mt[1]) * 1000000 + ((int)($mt[0] * 1000000));
    }
}