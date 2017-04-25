<?php


namespace IqOptionTest\Message;


class SuccessResponse implements MessageInterface
{

    public function toArray(): array
    {
        return [];
    }

    public static function fromArray(array $array): MessageInterface
    {
        return new SuccessResponse();
    }
}