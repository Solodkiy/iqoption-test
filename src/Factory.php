<?php


namespace IqOptionTest;

use IqOptionTest\Money\JsonMoneySerializer;
use IqOptionTest\Money\MoneySerializerInterface;
use Predis\Client;

class Factory
{

    public static function getMoneySerializer(): MoneySerializerInterface
    {
        return memorize(function () {
            return new JsonMoneySerializer();
        });
    }

    public static function getRedisClient(): Client
    {
        return memorize(function () {
            return new Client([
                'scheme' => 'tcp',
                'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            ]);
        });
    }

}