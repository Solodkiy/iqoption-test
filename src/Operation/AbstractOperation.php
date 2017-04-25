<?php


namespace IqOptionTest\Operation;


use IqOptionTest\Message\MessageInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractOperation implements MessageInterface
{
    /**
     * @var UuidInterface
     */
    private $operationId;

    /**
     * AbstractOperation constructor.
     * @param UuidInterface $operationId
     */
    public function __construct(?UuidInterface $operationId = null)
    {
        if (is_null($operationId)) {
            $operationId = Uuid::uuid1();
        }
        $this->operationId = $operationId;
    }

    /**
     * @return UuidInterface
     */
    public function getOperationId(): UuidInterface
    {
        return $this->operationId;
    }

}