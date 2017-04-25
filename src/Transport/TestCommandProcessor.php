<?php


namespace IqOptionTest\Transport;


use IqOptionTest\Transport\Envelope;
use IqOptionTest\Operation\AbstractOperation;
use IqOptionTest\Transport\Queue;
use Predis\Client;
use Ramsey\Uuid\Uuid;

class TestCommandProcessor
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * TestCommandProcessor constructor.
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }


    public function processOperation(AbstractOperation $operation)
    {
        $resultQueueName = 'result_'.Uuid::uuid1()->toString();
        $resultQueue = new Queue($resultQueueName, $this->redis);

        $operationEnvelope = Envelope::create($operation, $resultQueueName);

        $queue = new Queue('work', $this->redis);
        $queue->push($operationEnvelope);

        $answer= $resultQueue->getMessageWithBlock(10);
        if ($answer) {
            $queue->acknowledge($answer);
            $message = $answer->getMessage();
            return $message;
        } else {
            return null;
        }
    }

}