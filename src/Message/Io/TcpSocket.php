<?php

namespace Orlion\CatAgentPhp\Message\Io;

use Orlion\CatAgentPhp\Exception\IoException;
use Orlion\CatAgentPhp\Message\MessageTree;

/**
 * TcpSocket
 *
 * @author Orlion <orlionml@gmail.com>
 * @package Orlion\CatAgentPhp\Message\Io
 */
class TcpSocket
{
    /**
     *
     */
    const DEFAULT_PORT = 2280;

    /**
     * @var string
     */
    private $serverAddr;
    /**
     * @var int
     */
    private $domain;
    /**
     * @var false|mixed|string
     */
    private $address;
    /**
     * @var int|mixed|string
     */
    private $port;
    /**
     * @var Codec
     */
    private $codec;
    /**
     * @var
     */
    private $socket;
    /**
     * @var bool
     */
    private $connected = false;

    /**
     * @param string $serverAddr
     * @throws IoException
     */
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
                throw new IoException(sprintf('server\'s address: %s is invalid',  $serverAddr));
            }
        }

        $this->codec = new Codec();
    }

    /**
     * @param MessageTree $tree
     * @return void
     * @throws IoException
     */
    public function send(MessageTree $tree): void
    {
        $this->connect();
        $request = $this->codec->encodeSendMessageRequest($tree);
        $this->writeRequest($request);
    }

    /**
     * @param string $domain
     * @return string
     * @throws IoException
     */
    public function createMessageId(string $domain): string
    {
        $this->connect();
        $request = $this->codec->encodeCreateMessageIdRequest($domain);
        $this->writeRequest($request);
        $response = $this->readResponse();
        if ($response[0] !== Codec::STATUS_OK)
        {
            throw new IoException(sprintf('create messageId failed with status: %d', $response[0]));
        }

        return $response[2];
    }

    /**
     * @return void
     * @throws IoException
     */
    protected function connect(): void
    {
        if ($this->connected) {
            return ;
        }

        if (empty($this->socket)) {
            $this->socket = socket_create($this->domain, SOCK_STREAM, 0);
            if ($this->socket === false) {
                throw new IoException(sprintf('create socket to %s failed', $this->serverAddr));
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
            list($errno, $errMsg) = $this->getLastSocketErr();
            throw new IoException(sprintf('socket connect to %s failed, address: %s, errno: %d, errMsg: %s', $this->serverAddr, $this->address, $errno, $errMsg));
        }

        $this->connected = true;
    }

    /**
     * @param string $request
     * @return void
     * @throws IoException
     */
    protected function writeRequest(string $request): void
    {
        $length = strlen($request);
        while (true) {
            $sent = @socket_write($this->socket, $request, $length);
            if ($sent === false) {
                list($errno, $errMsg) = $this->getLastSocketErr();
                $this->close();
                throw new IoException(sprintf('write request to %s failed, errno: %d, errMsg: %s', $this->serverAddr, $errno, $errMsg));
            }
            if ($sent < $length) {
                $request = substr($request, $sent);
                $length -= $sent;
            } else {
                break;
            }
        }
    }

    /**
     * @return array
     * @throws IoException
     */
    protected function readResponse(): array
    {
        $len = socket_recv($this->socket, $header, Codec::RESPONSE_HEADER_LEN, MSG_WAITALL);
        if ($len === 0) {
            $this->close();
            throw new IoException(sprintf('read response header from %s failed, connection closed', $this->serverAddr));
        } else if ($len === false) {
            list($errno, $errMsg) = $this->getLastSocketErr();
            $this->close();
            throw new IoException(sprintf('read response header from %s failed, errno: %d, errMsg: %s', $this->serverAddr, $errno, $errMsg));
        }

        list($status, $length) = $this->codec->decodeResponseHeader($header);

        $payload = '';
        if ($length > Codec::RESPONSE_HEADER_LEN) {
            $len = socket_recv($this->socket, $payload, $length - Codec::RESPONSE_HEADER_LEN, MSG_WAITALL);
            if ($len === 0) {
                $this->close();
                throw new IoException(sprintf('read response payload from %s failed, connection closed', $this->serverAddr));
            } else if ($len === false) {
                list($errno, $errMsg) = $this->getLastSocketErr();
                $this->close();
                throw new IoException(sprintf('read response payload from %s failed, errno: %d, errMsg: %s', $this->serverAddr, $errno, $errMsg));
            }
        }

        return [$status, $length, $payload];
    }

    /**
     * @return array
     */
    protected function getLastSocketErr(): array
    {
        $errno = socket_last_error($this->socket);
        return [$errno, socket_strerror($errno)];
    }

    /**
     * @return void
     */
    protected function close(): void
    {
        if ($this->connected) {
            socket_close($this->socket);
            $this->connected = false;
        }
        $this->socket = null;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }
}