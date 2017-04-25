<?php


namespace IqOptionTest\Transport;

use IqOptionTest\Message\MessageInterface;
use IqOptionTest\Transport\Envelope;
use Ramsey\Uuid\Uuid;

class EnvelopeSerializer
{
    public function toString(Envelope $envelope): string
    {
        return json_encode($envelope);
    }

    public function fromString(string $string): Envelope
    {
        $array = json_decode($string, true);
        if (is_array($array)) {
            $message = $this->extractMessage($array['message_class'], $array['message']);

            return new Envelope($message, $array['answer_to']);

        }
        throw new \RuntimeException('Error unserialize message');
    }

    private function extractMessage($messageClass, $messageString): MessageInterface
    {
        $classObject = new \ReflectionClass($messageClass);
        if ($classObject->implementsInterface(MessageInterface::class)) {
            $message = $messageClass::fromArray($messageString);
            if ($message instanceof MessageInterface) {
                return $message;
            }
        }
        throw new \RuntimeException('Error unserialize message');
    }
}