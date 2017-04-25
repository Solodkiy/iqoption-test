<?php


namespace IqOptionTest;

use IqOptionTest\Exception\DoubleOperationException;
use IqOptionTest\Exception\NotEnoughMoneyException;
use Money\Currency;
use Money\Money;
use Predis\Client;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

class DatabaseGateway
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * DatabaseGateway constructor.
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param $accountId
     * @param Money $amount
     * @param null|UuidInterface $operationId
     */
    public function draw($accountId, Money $amount, ?UuidInterface $operationId = null): void
    {
        $operationIdString = $operationId ? $operationId->toString() : '';
        $result = $this->redis->eval($this->getScript('draw'), 0, $accountId, $amount->getAmount(), $operationIdString);
        $this->processResult($result);
    }

    public function deposit($accountId, Money $amount, ?UuidInterface $operationId = null): void
    {
        $operationIdString = $operationId ? $operationId->toString() : '';
        $result = $this->redis->eval($this->getScript('deposit'), 0, $accountId, $amount->getAmount(), $operationIdString);
        $this->processResult($result);
    }

    public function getBalance($accountId): Money
    {
        $balanceString = $this->redis->hget('account_balance', $accountId);
        return new Money($balanceString, new Currency('USD'));
    }

    /**
     * @param $fromAccountId
     * @param $toAccountId
     * @param Money $amount
     * @param null|UuidInterface $operationId
     */
    public function transfer($fromAccountId, $toAccountId, Money $amount, ?UuidInterface $operationId = null)
    {
        Assert::notSame($fromAccountId, $toAccountId);
        $operationIdString = $operationId ? $operationId->toString() : '';
        $result = $this->redis->eval($this->getScript('transfer'), 0, $fromAccountId, $toAccountId, $amount->getAmount(), $operationIdString);
        $this->processResult($result);
    }

    private function getScript($name)
    {
        $script = $this->loadScriptFile($name);
        $script = str_replace('{{operation_check}}', $this->loadScriptFile('_operation_check'), $script);
        return $script;
    }

    private function loadScriptFile($name)
    {
        $fileName = __DIR__.'/../resource/script/'.$name.'.lua';
        if (file_exists($fileName)) {
            return file_get_contents($fileName);
        }
        throw new \RuntimeException('Script '.$name.' not found');
    }


    /**
     * @param $result
     * @throws \RuntimeException
     */
    private function processResult($result): void
    {
        if ($result != 'OK') {
            $map = [
                'NO_MONEY' => new NotEnoughMoneyException(),
                'DOUBLE_OPERATION' => new DoubleOperationException(),
            ];
            throw ($map[$result] ?? new \RuntimeException($result));
        }
    }
}