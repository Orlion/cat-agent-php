<?php

namespace Orlion\CatAgentPhp\Message\Io;

use Orlion\CatAgentPhp\Message\Codec\PlainTextMessageCodec;
use Orlion\CatAgentPhp\Message\MessageTree;
use RuntimeException;

class TcpSocketSender implements MessageSender
{
    const DEFAULT_PORT = 2280;

    private $socket;
    private $domain;
    private $address;
    private $port;
    private $codec;

    public function __construct(string $server)
    {
        if (strpos($server, 'unix:') === 0) {
            $this->domain = AF_UNIX;
            $this->address = $server;
        } else {
            $this->domain = AF_INET;
            $serverArr = explode(':', $server);
            $serverArrCount = count($serverArr);
            if ($serverArrCount == 1) {
                $this->address = $serverArr[0];
                $this->port = self::DEFAULT_PORT;
            } else if ($serverArrCount == 2) {
                if ($serverArr[0] === '') {
                    $this->address = '127.0.0.1';
                } else {
                    $this->address = $serverArr[0];
                }
                $this->port = $serverArr[1];
            } else {
                throw new RuntimeException('Agent\'s address: '. $server .' is invalid');
            }
        }

        $this->codec = new PlainTextMessageCodec();
    }

    public function send(MessageTree $tree): void
    {
        $data = $this->codec->encode($tree);
        var_dump($data);
        if ($this->conn()) {
            $data = $this->codec->encode($tree);
            socket_write($this->socket, $data, strlen($data));
        } else {
            // todo: log?
        }
    }

    protected function conn(): bool
    {
        if (is_null($this->socket)) {
            $socket = socket_create($this->domain, SOCK_STREAM, SOL_TCP);
            socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, ['sec'  =>  0, 'usec'   =>  10000]);
            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec'  =>  0, 'usec'   =>  1]);
    
            if ($this->domain == AF_UNIX) {
                $conn = socket_connect($socket, $this->address);
            } else {
                $conn = socket_connect($socket, $this->address, $this->port);
            }

            if ($conn) {
                $this->socket = $socket;
                return true;
            }

            return false;
        } else {
            return true;
        }
    }

    protected function close(): void
    {
        if (!is_null($this->socket)) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}