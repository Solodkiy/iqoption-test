<?php

namespace IqOptionTest;

use IqOptionTest\Exception\DoubleOperationException;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Ramsey\Uuid\Uuid;

class DatabaseGatewayTest extends TestCase
{
    public function testOperationCheck()
    {
        $gateway = $this->getGateway();

        $o1 = Uuid::uuid1();
        $gateway->deposit(1, new Money(10, new Currency('USD')), $o1);
        $o2 = Uuid::uuid1();
        $gateway->deposit(1, new Money(10, new Currency('USD')), $o2);

        $this->expectException(DoubleOperationException::class);
        $gateway->deposit(1, new Money(10, new Currency('USD')), $o1);
    }

    public function testOperationCheckWithoutCheck()
    {
        $gateway = $this->getGateway();
        $gateway->deposit(1, new Money(1, new Currency('USD')));
        $gateway->deposit(1, new Money(1, new Currency('USD')));
        $gateway->deposit(1, new Money(1, new Currency('USD')));

        $this->assertEquals(new Money(3, new Currency('USD')), $gateway->getBalance(1));
    }

    /**
     * Проверяем корректность вытиснения из operationsSet
     */
    public function testOperationCheckMax()
    {
        $gateway = $this->getGateway();

        $setSize = 1000;
        $ids = [];
        for ($i = 0; $i < $setSize+1; $i++) {
            $id = Uuid::uuid1();
            $gateway->deposit(1, new Money(1, new Currency('USD')), $id);

            $ids[$i] = $id;
        }

        foreach ($ids as $id) {
            $gateway->deposit(1, new Money(1, new Currency('USD')), $id);
        }
        $this->assertEquals(new Money(($setSize+1)*2, new Currency('USD')), $gateway->getBalance(1));
    }

    /**
     * @return DatabaseGateway
     */
    private function getGateway()
    {
        $client = new Client();
        $client->flushall();

        return new DatabaseGateway($client);
    }


}