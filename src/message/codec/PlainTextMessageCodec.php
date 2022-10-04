<?php

namespace Orlion\CatAgentPhp\Message\Codec;

use DateTime;
use Exception;
use Orlion\CatAgentPhp\Message\Event;
use Orlion\CatAgentPhp\Message\Message;
use Orlion\CatAgentPhp\Message\MessageTree;
use Orlion\CatAgentPhp\Message\Metric;
use Orlion\CatAgentPhp\Message\Transaction;
use RuntimeException;

class PlainTextMessageCodec implements MessageCodec
{
    const TAB = "\t";
    const LF = "\n";

    public function encode(MessageTree $tree): string
    {
        $buf = $this->encodeHeader($tree);
        
        $buf .= $this->encodeMessage($tree->getMessage());

        return $buf;
    }

    protected function encodeHeader(MessageTree $tree): string
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

    public function encodeMessage(Message $message)
    {
        if ($message instanceof Transaction) {
            $buf = $this->encodeTransaction($message);
        } else if ($message instanceof Event) {
            $buf = $this->encodeLine($message, 'E');
        } else {
            throw new RuntimeException("Unsupported message type");
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

    private function encodeLine(Message $message, string $type)
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
            $elements[] = $message->getRawDurationInMicros();
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
}