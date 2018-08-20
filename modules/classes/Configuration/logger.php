<?php

namespace Configuration;

use User\User;

class logger
{
    private static function getServerInfo()
    {
        $result['server']['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        $result['server']['REQUEST_ TIME'] = $_SERVER['REQUEST_TIME'];
        $result['server']['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        $result['server']['HTTP_ACCEPT'] = $_SERVER['HTTP_ACCEPT'];
        $result['server']['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $result['server']['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
        return $result;
    }

    public static function log($data)
    {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        $result['time'] = $d->format('Y-m-d H:i:s.v');

        $result += self::getServerInfo();

        $debug_backtrace = debug_backtrace();
        foreach ($debug_backtrace as $key => $el) {
            $result['server']['STACKTRACE'][$key] = ($el['class']) ? $el['class'] . '::' : "";
            $result['server']['STACKTRACE'][$key] .= $el['function'] . '() on ' . $el['line'] . ' line at ' . $el['file'];

        }
        $result['server']['USER']['id'] = (User::$user) ? User::$user['id'] : null;
        $result['data'] = $data;

        $filename = $_SERVER['DOCUMENT_ROOT'] . '/log/logs.txt';
        return file_put_contents($filename, json_encode($result) . "\n", FILE_APPEND);
    }
}