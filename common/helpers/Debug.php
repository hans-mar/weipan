<?php

namespace common\helpers;

class Debug
{
    private static $flag = 1;

    private static $_microtime = [];

    private static $_memory = [];

    public static function log($tagName = '')
    {
        $flag = str_pad(self::$flag++, 3, '0', STR_PAD_LEFT) . '. ';
        if ($tagName) {
            $tagName = ' -> ' . $tagName;
        }
        $memory = memory_get_usage(true);
        $microtime = microtime(true);
        $lastMemory = end(self::$_memory);
        $lastTime = end(self::$_microtime);
        self::$_memory[] = $memory;
        self::$_microtime[] = $microtime;
        $diffMemory = $lastMemory ? 'diffMemory：' . self::formatMemory($memory - $lastMemory) : '';
        $diffTime = $lastTime ? 'diffTime：' . number_format(($microtime - $lastTime), 3) * 1000 . '(ms)' : '';
        $eol = PHP_SAPI === 'cli' ? PHP_EOL : '<br>';
        $items = [
            'nowTime：' . $microtime,
            'nowMemory：' . self::formatMemory($memory)
        ];
        if ($diffTime) {
            $items[] = $diffTime;
        }
        if ($diffMemory) {
            $items[] = $diffMemory;
        }
        echo "{$flag}" . implode('，', $items) . "{$tagName}{$eol}";
    }

    private static function formatMemory($memoryUsage)
    {
        if ($memoryUsage < 1024) {
            return $memoryUsage . '(bytes)';
        } elseif ($memoryUsage < 1024 * 1024) {
            return round($memoryUsage / 1024, 3) . '(KB)';
        } else {
            return round($memoryUsage / 1024 / 1024, 3) . '(MB)';
        }
    }
}
