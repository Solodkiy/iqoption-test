<?php

namespace IqOptionTest\Operation;

use IqOptionTest\Factory;
use IqOptionTest\Message\MessageInterface;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TransferOperation extends AbstractOperation
{

    /**
     * @var int
     */
    private $fromAccountId;

    /**
     * @var int
     */
    private $toAccountId;

    /**
     * @var Money
     */
    private $amount;

    /**
     * DebitOperation constructor.
     * @param int $fromAccountId
     * @param int $toAccountId
     * @param Money $amount
     * @param null|UuidInterface $operationId
     */
    public function __construct(int $fromAccountId, int $toAccountId, Money $amount, ?UuidInterface $operationId = null)
    {
        parent::__construct($operationId);
        $this->amount = $amount;
        $this->fromAccountId = $fromAccountId;
        $this->toAccountId = $toAccountId;
    }


    public function toArray(): array
    {
        return [
            'from_account_id' => $this->fromAccountId,
            'to_account_id' => $this->toAccountId,
            'amount' => Factory::getMoneySerializer()->format($this->amount),
            'operation_id' => $this->getOperationId()->toString(),
        ];
    }

    public static function fromArray(array $array): MessageInterface
    {
        return new self(
            $array['from_account_id'],
            $array['to_account_id'],
            Factory::getMoneySerializer()->parse($array['amount']),
            Uuid::fromString($array['operation_id'])
        );
    }

    /**
     * @return int
     */
    public function getFromAccountId(): int
    {
        return $this->fromAccountId;
    }

    public function getToAccountId(): int
    {
        return $this->toAccountId;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }
}