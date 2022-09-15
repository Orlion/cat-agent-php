<?php

namespace Orlion\CatAgentPhp\Message\Codec;

use Orlion\CatAgentPhp\Message\MessageTree;

class PlainTextMessageCodec implements MessageCodec
{
    const VERSION = 'PT1';
    const HOSTNAME_PLACEHOLDER = '{hostname}';
    const IP_PLACEHOLDER = '{ip}';
    const MESSAGE_ID_PLACEHOLDER = '{messageId}';

    public function encode(MessageTree $tree): string
    {
        $header = $this->encodeHeader($tree);
    }

    protected function encodeHeader(MessageTree $tree): string
    {
        $messageId = $tree->getMessageId();
        if ($messageId === '') {
            $messageId = self::MESSAGE_ID_PLACEHOLDER;
        }

        return sprintf("%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n", self::VERSION, $tree->getDomain(), self::HOSTNAME_PLACEHOLDER, self::IP_PLACEHOLDER, $tree->getThreadGroupName(), $tree->getThreadId(), $tree->getThreadName(), $messageId, $tree->getParentMessageId(), $tree->getRootMessageId(), '');
    }
}

