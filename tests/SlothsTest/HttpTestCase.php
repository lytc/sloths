<?php

namespace SlothsTest;

use Sloths\Http\Client;

class HttpTestCase extends TestCase
{
    const WEB_SERVER_HOST = '0.0.0.0';
    const WEB_SERVER_PORT = 8008;
    const TIMEOUT = .5;

    protected static $docRoot;
    protected static $pid;

    public static function setUpBeforeClass()
    {
        $command = sprintf(
            'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
            self::WEB_SERVER_HOST,
            self::WEB_SERVER_PORT,
            self::$docRoot
        );

        $output = array();
        exec($command, $output);
        self::$pid = (int) $output[0];

        self::ensureReadyToConnect();
    }

    public static function ensureReadyToConnect()
    {
        $connected = false;
        $time = microtime(true);

        set_error_handler(function() {});
        while (microtime(true) - $time <= self::TIMEOUT) {
            try {
                $response = Client::get(self::WEB_SERVER_HOST . ':', self::WEB_SERVER_PORT)->send();
            } catch(\Exception $e) {

            }

            if ($response) {
                $connected = true;
                break;
            }
        }

        restore_error_handler();

        if (!$connected) {
            throw new \RuntimeException('Could not connect to web server');
        }
    }

    public static function tearDownAfterClass()
    {
        exec('kill ' . self::$pid);
    }

    protected static function getScriptUrl($script)
    {
        return sprintf('http://%s:%s/%s', self::WEB_SERVER_HOST, self::WEB_SERVER_PORT, $script);
    }

    public function get($script)
    {
        $url = self::getScriptUrl($script);
        return Client::get($url);
    }
}