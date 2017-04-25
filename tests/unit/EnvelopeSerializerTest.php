<?php

namespace IqOptionTest;

use IqOptionTest\Operation\GetBalanceOperation;
use IqOptionTest\Transport\Envelope;
use IqOptionTest\Transport\EnvelopeSerializer;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class EnvelopeSerializerTest extends TestCase
{
    public function testSerialize()
    {
        $message = new GetBalanceOperation(15);
        $envelope = Envelope::create($message, 'answer');

        $serializer = new EnvelopeSerializer();
        $string = $serializer->toString($envelope);
        $this->assertInternalType('string', $string);

        $envelope2 = $serializer->fromString($string);
        $this->assertEquals($envelope, $envelope2);
    }

    public function testIncorrectDeserialize()
    {
        $this->expectException(RuntimeException::class);

        $serializer = new EnvelopeSerializer();
        $serializer->fromString('incorrect');
    }

}
