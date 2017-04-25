<?php

namespace IqOptionTest\Operation;

use IqOptionTest\Factory;
use IqOptionTest\Message\MessageInterface;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DepositOperation extends AbstractOperation
{

    /**
     * @var int
     */
    private $accountId;

    /**
     * @var Money
     */
    private $amount;

    /**
     * DebitOperation constructor.
     * @param int $accountId
     * @param Money $amount
     * @param null|UuidInterface $operationId
     */
    public function __construct(int $accountId, Money $amount, ?UuidInterface $operationId = null)
    {
        parent::__construct($operationId);
        $this->accountId = $accountId;
        $this->amount = $amount;
    }


    public function toArray(): array
    {
        return [
            'account_id' => $this->accountId,
            'amount' => Factory::getMoneySerializer()->format($this->amount),
            'operation_id' => $this->getOperationId()->toString(),
        ];
    }

    public static function fromArray(array $array): MessageInterface
    {
        return new self(
            $array['account_id'],
            Factory::getMoneySerializer()->parse($array['amount']),
            Uuid::fromString($array['operation_id'])
        );
    }

    /**
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }
}