<?php

namespace IqOptionTest\Message;

interface MessageInterface
{

    public function toArray(): array;

    public static function fromArray(array $array): MessageInterface;
}