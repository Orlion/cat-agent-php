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
    const VERSION = 'PT1';
    const TAB = "\t";
    const LF = "\n";
    const HOSTNAME_PLACEHOLDER = '{hostname}';
    const IP_PLACEHOLDER = '{ip}';
    const MESSAGE_ID_PLACEHOLDER = '{messageId}';

    const POLICY_DEFAULT = 0;
    const POLICY_WITHOUT_STATUS = 1;
    const POLICY_WITH_DURATION = 2;

    public function encode(MessageTree $tree): string
    {
        $buf = $this->encodeHeader($tree);
        
        if (!is_null($tree->getMessage())) {
            $buf .= $this->encodeMessage($tree->getMessage());
        }

        $len = strlen($buf);
        
        return pack('N', $len) . pack("a{$len}", $buf);
    }

    protected function encodeHeader(MessageTree $tree): string
    {
        $messageId = $tree->getMessageId();
        if ($messageId === '') {
            $messageId = self::MESSAGE_ID_PLACEHOLDER;
        }

        return sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n", self::VERSION, $tree->getDomain(), self::HOSTNAME_PLACEHOLDER, self::IP_PLACEHOLDER, $tree->getThreadGroupName(), $tree->getThreadId(), $tree->getThreadName(), $messageId, $tree->getParentMessageId(), $tree->getRootMessageId(), '');
    }

    public function encodeMessage(Message $message)
    {
        if ($message instanceof Transaction) {
            $buf = $this->encodeTransaction($message);
        } else if ($message instanceof Event) {
            $buf = $this->encodeLine($message, 'E', self::POLICY_DEFAULT);
        } else if ($message instanceof Metric) {
            $buf = $this->encodeLine($message, 'M', self::POLICY_DEFAULT);
        } else {
            throw new RuntimeException("Unsupported message type.");
        }
        return $buf;
    }

    protected function encodeTransaction(Transaction $transaction)
    {
        $children = $transaction->getChildren();
        if (empty($children)) {
            return $this->encodeLine($transaction, 'A', self::POLICY_WITH_DURATION);
        } else {
            $buf = $this->encodeLine($transaction, 't', self::POLICY_WITHOUT_STATUS);

            foreach ($children as $child) {
                if (!is_null($child)) {
                    $buf .= $this->encodeMessage($child);
                }
            }

            $buf .= $this->encodeLine($transaction, 'T', self::POLICY_WITH_DURATION);

            return $buf;
        }
    }

    private function encodeLine(Message $message, string $type, int $policy)
    {
        $elements = [$type];

        if ($type === 'T' && $message instanceof Transaction) {
            $duration = $message->getDurationInMillis();

            $elements[] = $this->formatTime($message->getTimestamp() + $duration);
        } else {
            $elements[] =  $this->formatTime($message->getTimestamp());
        }

        $elements[] = $message->getType();
        $elements[] = $message->getName();

        if($policy != self::POLICY_WITHOUT_STATUS) {
            $elements[] = $message->getStatus();

            $data = $message->getData();

            if ($policy == self::POLICY_WITH_DURATION && $message instanceof Transaction) {
                $elements[] = $message->getRawDurationInMicros() . 'us';
            }

            if (!is_scalar($data)) {
                $elements[] = json_encode($data, JSON_UNESCAPED_UNICODE);
            } else {
                $elements[] = $data;
            }
        }

        return implode(self::TAB, $elements) . self::TAB . self::LF;
    }

    private function formatTime(int $timestamp)
    {
        return date('Y-m-d H:i:s.', (int) $timestamp / 1000) . substr($timestamp, -3);
    }
}