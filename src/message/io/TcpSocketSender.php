<?php

namespace Orlion\CatAgentPhp\Message\Io;

use Orlion\CatAgentPhp\Message\MessageTree;

class TcpSocketSender implements MessageSender
{
    private static $instance;

    private function __construct()
    {
        
    }

    public static function getInstance(): TcpSocketSender
    {
        if (is_null(self::$instance)) {
            self::$instance = new TcpSocketSender();
        }

        return self::$instance;
    }

    public function send(MessageTree $tree): void
    {
        $socket = socket_create(AF_UNIX, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, ['sec'  =>  0, 'usec'   =>  10000]);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec'  =>  0, 'usec'   =>  1]);

        $address = '';
        $conn = socket_connect($socket, $address);
        if ($conn) {
            socket_write($socket, '', '');
        } else {
            // todo: log?
        }
    }
}