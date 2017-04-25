<?php


namespace IqOptionTest\Message;


use IqOptionTest\Factory;
use Money\Money;
use Webmozart\Assert\Assert;

class BalanceStatus implements MessageInterface
{
    const STATUS_SUCCESS = 'success';

    private $accountId;

    /**
     * @var Money
     */
    private $balance;


    private $status;

    /**
     * BalanceStatus constructor.
     * @param $accountId
     * @param Money $balance
     * @param $status
     */
    public function __construct(int $accountId, Money $balance, $status)
    {
        $this->accountId = $accountId;
        $this->balance = $balance;

        Assert::oneOf($status, [self::STATUS_SUCCESS]);
        $this->status = $status;
    }


    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'account_id' => $this->accountId,
            'balance' => Factory::getMoneySerializer()->format($this->balance)
        ];
    }

    public static function fromArray(array $array): MessageInterface
    {
        return new BalanceStatus(
            $array['account_id'],
            Factory::getMoneySerializer()->parse($array['balance']),
            $array['status']
        );
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return Money
     */
    public function getBalance(): Money
    {
        return $this->balance;
    }

    /**
     * @return string
     */
    public function isSuccess()
    {
        return $this->status == self::STATUS_SUCCESS;
    }
}