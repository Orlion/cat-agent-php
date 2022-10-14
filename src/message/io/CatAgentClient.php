<?php

namespace Orlion\CatAgentPhp\Message\Io;

use Orlion\CatAgentPhp\Exception\CatAgentException;
use Orlion\CatAgentPhp\Message\MessageTree;

class CatAgentClient
{
    const DEFAULT_PORT = 2280;

    private $serverAddr;
    private $domain;
    private $address;
    private $port;
    private $codec;
    private $socket;
    private $connected = false;

    public function __construct(string $serverAddr)
    {
        $this->serverAddr = $serverAddr;
        if (strpos($serverAddr, 'unix://') === 0) {
            $this->domain = AF_UNIX;
            $this->address = substr($serverAddr, 7);
        } else {
            $this->domain = AF_INET;
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
                throw new CatAgentException(sprintf('server\'s address: %s is invalid',  $serverAddr));
            }
        }

        $this->codec = new Codec();
    }

    public function send(MessageTree $tree): void
    {
        $this->connect();
        $request = $this->codec->encodeSendMessageRequest($tree);
        $this->writeRequest($request);
    }

    public function createMessageId(string $domain): string
    {
        $this->connect();
        $request = $this->codec->encodeCreateMessageIdRequest($domain);
        $this->writeRequest($request);
        list($status, $length, $messageId) = $this->readResponse();
        if ($status !== Codec::STATUS_OK) {
            throw new CatAgentException(sprintf('%s response unsuccessful status: %d', $this->serverAddr, $status));
        }

        return $messageId;
    }

    protected function connect()
    {
        if ($this->connected) {
            return ;
        }

        if (empty($this->socket)) {
            $this->socket = socket_create($this->domain, SOCK_STREAM, 0);
            if ($this->socket === false) {
                throw new CatAgentException(sprintf('create socket to %s failed', $this->serverAddr));
            }
        }

        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, ['sec'  =>  0, 'usec'   =>  10000]);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec'  =>  0, 'usec'   =>  10000]);

        if ($this->domain == AF_UNIX) {
            $conn = @socket_connect($this->socket, $this->address);
        } else {
            $conn = @socket_connect($this->socket, $this->address, $this->port);
        }

        if ($conn === false) {
            list($errno, $errmsg) = $this->getLastSocketErr();
            throw new CatAgentException(sprintf('socket connect to %s failed, address: %s, errno: %d, errmsg: %s', $this->serverAddr, $this->address, $errno, $errmsg));
        }

        $this->connected = true;
    }

    protected function writeRequest(string $request)
    {
        echo $request . PHP_EOL;
        $length = strlen($request);
        while (true) {
            $sent = socket_write($this->socket, $request, $length);
            if ($sent === false) {
                $this->close();
                list($errno, $errmsg) = $this->getLastSocketErr();
                throw new CatAgentException(sprintf('write request to %s failed, errno: %d, errmsg: %s', $this->serverAddr, $errno, $errmsg));
            }
            if ($sent < $length) {
                $request = substr($request, $sent);
                $length -= $sent;
            } else {
                break;
            }
        }
    }

    protected function readResponse(): array
    {
        $len = socket_recv($this->socket, $header, Codec::RESPONSE_HEADER_LEN, MSG_WAITALL);
        if ($len === 0) {
            $this->close();
            throw new CatAgentException(sprintf('read response header from %s failed, connection closed', $this->serverAddr));
        } else if ($len === false) {
            $this->close();
            list($errno, $errmsg) = $this->getLastSocketErr();
            throw new CatAgentException(sprintf('read response header from %s failed, errno: %d, errmsg: %s', $this->serverAddr, $errno, $errmsg));
        }

        list($status, $length) = $this->codec->decodeResponseHeader($header);
        if ($status !== Codec::STATUS_OK)
        {
            throw new CatAgentException(sprintf('%s response unsuccessful status: %d', $this->serverAddr, $status));
        }

        $payload = '';
        if ($length > Codec::RESPONSE_HEADER_LEN) {
            $len = socket_recv($this->socket, $payload, $length - Codec::RESPONSE_HEADER_LEN, MSG_WAITALL);
            if ($len === 0) {
                $this->close();
                throw new CatAgentException(sprintf('read response payload from %s failed, connection closed', $this->serverAddr));
            } else if ($len === false) {
                $this->close();
                list($errno, $errmsg) = $this->getLastSocketErr();
                throw new CatAgentException(sprintf('read response payload from %s failed, errno: %d, errmsg: %s', $this->serverAddr, $errno, $errmsg));
            }
        }

        return [$status, $length, $payload];
    }

    protected function getLastSocketErr(): array
    {
        $errno = socket_last_error($this->socket);
        return [$errno, socket_strerror($errno)];
    }

    protected function close(): void
    {
        if ($this->connected) {
            socket_close($this->socket);
            $this->connected = false;
        }
        $this->socket = null;
    }

    public function __destruct()
    {
        $this->close();
    }
}