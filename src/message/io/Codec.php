<?php

namespace Orlion\CatAgentPhp\Message\Io;

use Orlion\CatAgentPhp\Exception\CatAgentException;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageTree;
use Orlion\CatAgentPhp\Message\Transaction;

class Codec
{
    const TAB = "\t";
    const LF = "\n";
    const CMD_CREATE_MESSGAE_ID = 1;
    const CMD_SEND_MESSAGE = 2;
    const REQUEST_HEADER_LEN = 8;
    const RESPONSE_HEADER_LEN = 8;
    const STATUS_OK = 0;

    public function encodeSendMessageRequest(MessageTree $tree): string
    {
        $payload = $this->encodeTreeHeader($tree) . $this->encodeMessage($tree->getMessage());
        return $this->encodeRequest(self::CMD_SEND_MESSAGE, $payload);
    }

    protected function encodeTreeHeader(MessageTree $tree): string
    {
        $elements = [
            $tree->getDomain(),
            $tree->getThreadGroupName(), 
            $tree->getThreadId(), 
            $tree->getThreadName(),
            $tree->getMessageId() ?? '',
            $tree->getParentMessageId(), 
            $tree->getRootMessageId(),
        ];

        return implode(self::TAB, $elements) . self::LF;
    }

    protected function encodeMessage(Message $message)
    {
        if ($message instanceof Transaction) {
            $buf = $this->encodeTransaction($message);
        } else if ($message instanceof Event) {
            $buf = $this->encodeLine($message, 'E');
        } else {
            throw new CatAgentException('encode message failed, unsupported message type');
        }
        return $buf;
    }

    protected function encodeTransaction(Transaction $transaction)
    {
        $children = $transaction->getChildren();
        if (empty($children)) {
            return $this->encodeLine($transaction, 'A');
        } else {
            $buf = $this->encodeLine($transaction, 't');

            foreach ($children as $child) {
                if (!is_null($child)) {
                    $buf .= $this->encodeMessage($child);
                }
            }

            $buf .= $this->encodeLine($transaction, 'T');

            return $buf;
        }
    }

    protected function encodeLine(Message $message, string $type)
    {
        $elements = [
            $type,
            $message->getType(),
            $message->getName(),
            $message->getStatus(),
        ];

        if ($type === 'T' && $message instanceof Transaction) {
            $duration = $message->getDurationInMillis();
            $elements[] = $message->getTimestamp() + $duration;
        } else {
            $elements[] =  $message->getTimestamp();
        }

        if ($message instanceof Transaction) {
            $elements[] = $message->getDurationInMicros();
        } else {
            $elements[] = '';
        }

        $data = $message->getData();
        if (!is_null($data)) {
            $elements[] = json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            $elements[] = '';
        }

        return implode(self::TAB, $elements) . self::LF;
    }

    public function encodeCreateMessageIdRequest(string $domain): string
    {
        return $this->encodeRequest(self::CMD_CREATE_MESSGAE_ID, $domain);
    }

    protected function encodeRequest(int $cmd, string $payload): string
    {
        $payloadLen = strlen($payload);
        return pack('N', $cmd) . pack('N', $payloadLen + self::REQUEST_HEADER_LEN) . pack("a{$payloadLen}", $payload);
    }

    public function decodeResponseHeader(string $header)
    {
        $statusArr = unpack('N', substr($header, 0, 4));
        if (!isset($statusArr[1]))
        {
            throw new CatAgentException(sprintf('response header parse status failed, header: %s', $header));
        }

        $lengthArr = unpack('N', substr($header, 4, 4));
        if (!isset($lengthArr[1]))
        {
            throw new CatAgentException(sprintf('response header parse length failed, header: %s', $header));
        }
        $length = $lengthArr[1];
        if ($length < self::RESPONSE_HEADER_LEN) {
            throw new CatAgentException(sprintf('response header bad length: %d < %d', $length, self::RESPONSE_HEADER_LEN));
        }

        return [$statusArr[1], $length];
    }
}
