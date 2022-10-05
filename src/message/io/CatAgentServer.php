<?php

namespace Orlion\CatAgentPhp\Message\Io;

use Exception;
use Orlion\CatAgentPhp\Message\Codec\PlainTextMessageCodec;
use Orlion\CatAgentPhp\Message\MessageIdFactory;
use Orlion\CatAgentPhp\Message\MessageSender;
use Orlion\CatAgentPhp\Message\MessageTree;
use RuntimeException;

class CatAgentServer implements MessageIdFactory, MessageSender
{
    const DEFAULT_PORT = 2280;
    const CMD_GET_NEXT_ID = 1;
    const CMD_SEND_MESSAGE = 2;

    private $domain;
    private $domainLen;
    private $serverAddr;
    private $protocol;
    private $address;
    private $port;
    private $codec;
    private $socket;
    private $hasConn = false;

    public function __construct( string $domain, string $serverAddr)
    {
        $this->serverAddr = $serverAddr;
        if (strpos($serverAddr, 'unix:') === 0) {
            $this->protocol = AF_UNIX;
            $this->address = $serverAddr;
        } else {
            $this->protocol = AF_INET;
            $serverArr = explode(':', $serverAddr);
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
                throw new RuntimeException('Agent\'s address: '. $serverAddr .' is invalid');
            }
        }

        $this->domain = $domain;
        $this->domainLen = strlen($domain);

        $this->codec = new PlainTextMessageCodec();
    }

    public function send(MessageTree $tree): void
    {
        var_dump('a');
        if ($this->conn()) {
            var_dump('b');
            $body = $this->codec->encode($tree);
            $bodyLen = strlen($body);
            $data = pack('N', self::CMD_SEND_MESSAGE) . pack('N', $bodyLen + 8) . pack("a{$bodyLen}", $body);
            $length = strlen($data);
            echo $data;
            while (true) {
                $sent = socket_write($this->socket, $data, $length);
                var_dump(111111, $sent, $length);
                if ($sent === false) {
                    $this->hasConn = false;
                    list($errno, $errmsg) = $this->getLastSocketErr();
                    throw new Exception("send message to cat-agent server {$this->serverAddr} failed: [{$errno}] {$errmsg}");
                }
           
                if ($sent < $length) {
                    $data= substr($data, $sent);
                    $length -= $sent;
                } else {
                    break;
                }
            }
        } else {
            var_dump('c');
        }
    }

    public function getNextId(): string
    {
        if ($this->conn()) {
            $data = pack('N', self::CMD_GET_NEXT_ID) . pack('N', 8);
            $length = strlen($data);
            while (true) {
                $sent = socket_write($this->socket, $data, $length);
                if ($sent === false) {
                    list($errno, $errmsg) = $this->getLastSocketErr();
                    throw new Exception("get nextId from cat-agent server {$this->serverAddr} failed: [{$errno}] {$errmsg}");
                }
                if ($sent < $length) {
                    $data = substr($data, $sent);
                    $length -= $sent;
                } else {
                    break;
                }
            }

            $len = socket_recv($this->socket, $data, 8, MSG_WAITALL);
            if ($len === 0) {
                $this->hasConn = false;
                throw new RuntimeException('recv nextId read header failed: connection closed');
            } else if ($len === false) {
                list($errno, $errmsg) = $this->getLastSocketErr();
                throw new RuntimeException("recv nextId read header error: [{$errno}] {$errmsg}");
            }

            $statusArr = unpack('N', substr($data, 0, 4));
            if (!isset($statusArr[1]))
            {
                throw new RuntimeException("recv nextId parse status error");
            }
            $status = $statusArr[1];
            if ($status !== 0) {
                throw new RuntimeException("recv nextId failed, status: [{$status}]");
            }

            $responseLenArr = unpack('N', substr($data, 4, 4));
            if (!isset($responseLenArr[1]))
            {
                throw new RuntimeException("recv nextId parse response length error");
            }
            $responseLen = $responseLenArr[1];
            if ($responseLen <= 8) {
                throw new RuntimeException("recv nextId failed, response length: {$responseLen} <= 8");
            }

            $len = socket_recv($this->socket, $data, $responseLen - 8, MSG_WAITALL);
            if ($len === 0) {
                $this->hasConn = false;
                throw new RuntimeException('recv nextId read body failed: connection closed');
            } else if ($len === false) {
                list($errno, $errmsg) = $this->getLastSocketErr();
                throw new RuntimeException("recv nextId read body error: [{$errno}] {$errmsg}");
            }

            if (strlen($data, $this->domain) !== 0) {
                throw new RuntimeException("recv nextId domain: {$this->domain} is inconsistent with cat agent server's domain");
            }

            return $data;
        } else {
            list($errno, $errmsg) = $this->getLastSocketErr();
            throw new Exception("get nextId connect cat-agent server {$this->serverAddr} failed: [{$errno}] {$errmsg}");
        }
    }

    protected function conn(): bool
    {
        if ($this->hasConn) {
            return true;
        }

        if (is_null($this->socket)) {
            $this->socket = socket_create($this->protocol, SOCK_STREAM, SOL_TCP);
        }

        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, ['sec'  =>  0, 'usec'   =>  10000]);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec'  =>  0, 'usec'   =>  10000]);

        if ($this->protocol == AF_UNIX) {
            $conn = @socket_connect($this->socket, $this->address);
        } else {
            $conn = @socket_connect($this->socket, $this->address, $this->port);
        }

        if ($conn) {
            $this->hasConn = true;
            return true;
        }

        return false;
    }

    protected function getLastSocketErr(): array
    {
        $errno = socket_last_error($this->socket);
        return [$errno, socket_strerror($errno)];
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