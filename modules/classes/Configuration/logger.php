<?php

namespace Configuration;

use User\User;

class Logger
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

    public static function log($type, $data)
    {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        $result['time'] = $d->format('Y-m-d H:i:s.v');
        $result['type'] = $type;

        $result += self::getServerInfo();

        $debug_backtrace = debug_backtrace();
        foreach ($debug_backtrace as $key => $el) {
            $result['server']['STACKTRACE'][$key] = ($el['class']) ? $el['class'] . '::' : "";
            $result['server']['STACKTRACE'][$key] .= $el['function'] . '() on ' . $el['line'] . ' line at ' . $el['file'];
        }
        $result['server']['USER']['id'] = (User::$user) ? User::$user['id'] : null;
        $result['data'] = ($data) ? $data : null;

        $filename = $_SERVER['DOCUMENT_ROOT'] . '/log/logs.txt';
        $file_data = json_encode($result) . "\n";
        $file_data .= file_get_contents($filename);
        return file_put_contents($filename, $file_data);
    }

    public static function read($lines = 20)
    {
        $handle = @fopen($_SERVER['DOCUMENT_ROOT'] . '/log/logs.txt', "r");
        if ($handle) {
            $i = 0;
            $result = array();
            while (($buffer = fgets($handle, 4096)) !== false) {
                if ($i < $lines) {
                    $decoded = json_decode($buffer, true);
                    $str = $decoded['time'] . " " . $decoded['type'] . " from " . $decoded['server']['STACKTRACE'][0];
                    $str .= ($decoded['server']['data']) ? ' with data: ' . $decoded['server']['data'] : '';
                    $str .= ($decoded['server']['user']) ? ' from user: ' . $decoded['server']['user'] : '';
                    array_push($result, $str);
                    $i++;
                }
            }
            if (!feof($handle)) {
                echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
            }
            fclose($handle);
            return $result;
        }
    }

}