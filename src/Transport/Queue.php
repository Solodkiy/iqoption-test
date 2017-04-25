<?php

namespace IqOptionTest\Transport;

use IqOptionTest\Transport\Envelope;
use IqOptionTest\Transport\EnvelopeSerializer;
use Predis\Client;

class Queue
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var EnvelopeSerializer
     */
    private $serializer;

    /**
     * Queue constructor.
     * @param string $name
     * @param Client $redis
     */
    public function __construct(string $name, Client $redis)
    {
        $this->name = $name;
        $this->redis = $redis;
        $this->serializer = new EnvelopeSerializer();
    }

    public function getMessage(): ?Envelope
    {
        $message = $this->redis->rpoplpush($this->getQueueKey(), $this->getProcessingKey());
        return $this->createEnvelopeFromString($message);
    }

    public function getMessageWithBlock($timeoutSec): ?Envelope
    {
        $message = $this->redis->brpoplpush($this->getQueueKey(), $this->getProcessingKey(), $timeoutSec);
        return $this->createEnvelopeFromString($message);
    }

    private function createEnvelopeFromString(?string $message): ?Envelope
    {
        if (!is_null($message)) {
            return $this->serializer->fromString($message);
        }
        return null;
    }

    public function push(Envelope $envelope)
    {
        $this->redis->lpush($this->getQueueKey(), [$this->serializeEnvelope($envelope)]);
    }


    public function acknowledge(Envelope $envelope)
    {
        $this->redis->lrem($this->getProcessingKey(), 0, $this->serializeEnvelope($envelope));
    }

    private function serializeEnvelope(Envelope $envelope): string
    {
        return $this->serializer->toString($envelope);
    }

    private function getQueueKey(): string
    {
        return 'queue:'.$this->name;
    }

    private function getProcessingKey(): string
    {
        return 'queue:'.$this->name.':processing';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


}