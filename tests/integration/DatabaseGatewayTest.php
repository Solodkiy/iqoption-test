<?php

namespace IqOptionTest;

use IqOptionTest\Exception\DoubleOperationException;
use IqOptionTest\Exception\NotEnoughMoneyException;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Ramsey\Uuid\Uuid;

class DatabaseGatewayTest extends TestCase
{
    public function testOperationCheck()
    {
        $gateway = $this->getGateway();

        $o1 = Uuid::uuid1();
        $gateway->deposit(1, m(10), $o1);
        $o2 = Uuid::uuid1();
        $gateway->deposit(1, m(10), $o2);

        $this->expectException(DoubleOperationException::class);
        $gateway->deposit(1, m(10), $o1);
    }

    public function testOperationCheckWithoutCheck()
    {
        $gateway = $this->getGateway();
        $gateway->deposit(1, m(1));
        $gateway->deposit(1, m(1));
        $gateway->deposit(1, m(1));

        $this->assertEquals(m(3), $gateway->getBalance(1));
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
            $gateway->deposit(1, m(1), $id);

            $ids[$i] = $id;
        }

        foreach ($ids as $id) {
            $gateway->deposit(1, m(1), $id);
        }
        $this->assertEquals(m(($setSize+1)*2), $gateway->getBalance(1));
    }

    public function testDepositOperation()
    {
        $gateway = $this->getGateway();
        $gateway->deposit(1, m(100));
        $this->assertEquals(m(100), $gateway->getBalance(1));
        $this->assertEquals(m(0), $gateway->getBalance(2));

        $gateway->deposit(2, m(20));
        $this->assertEquals(m(100), $gateway->getBalance(1));
        $this->assertEquals(m(20), $gateway->getBalance(2));
    }

    public function testDrawOperation()
    {
        $gateway = $this->getGateway();
        $gateway->deposit(1, m(100));
        $this->assertEquals(m(100), $gateway->getBalance(1));

        $gateway->draw(1, m(10));
        $this->assertEquals(m(90), $gateway->getBalance(1));
        $gateway->draw(1, m(90));
        $this->assertEquals(m(0), $gateway->getBalance(1));

        $this->expectException(NotEnoughMoneyException::class);
        $gateway->draw(1, m(1));
    }

    public function testTransferOperation()
    {
        $gateway = $this->getGateway();
        $gateway->deposit(1, m(100));
        $gateway->deposit(2, m(10));
        $this->assertEquals(m(100), $gateway->getBalance(1));
        $this->assertEquals(m(10), $gateway->getBalance(2));

        $gateway->transfer(1, 2, m(55));
        $this->assertEquals(m(45), $gateway->getBalance(1));
        $this->assertEquals(m(65), $gateway->getBalance(2));

        $this->expectException(NotEnoughMoneyException::class);
        $gateway->transfer(1, 2, m(100));
    }

    /**
     * @return DatabaseGateway
     */
    private function getGateway()
    {
        $redisHost = getenv('TEST_REDIS');
        if (!$redisHost) {
            $redisHost = '127.0.0.1';
        }
        $client = new Client([
            'host' => $redisHost,
        ]);
        $client->flushall();

        return new DatabaseGateway($client);
    }


}