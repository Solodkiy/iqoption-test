<?php


namespace IqOptionTest\Operation;


use IqOptionTest\Message\MessageInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GetBalanceOperation extends AbstractOperation
{
    /**
     * @var int
     */
    private $accountId;

    public function __construct(int $accountId, ?UuidInterface $operationId = null)
    {
        parent::__construct($operationId);
        $this->accountId = $accountId;
    }

    public function toArray(): array
    {
        return [
            'account_id' => $this->accountId,
            'operation_id' => $this->getOperationId()->toString(),
        ];
    }

    public static function fromArray(array $data): MessageInterface
    {
        return new self($data['account_id'], Uuid::fromString($data['operation_id']));
    }

    /**
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }
}