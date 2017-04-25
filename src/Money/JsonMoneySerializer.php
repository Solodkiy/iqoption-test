<?php


namespace IqOptionTest\Money;


use Money\Currency;
use Money\Exception;
use Money\Money;

class JsonMoneySerializer implements MoneySerializerInterface
{

    /**
     * Parses a string into a Money object (including currency).
     *
     * @param string $money
     * @param string|null $forceCurrency
     *
     * @return Money
     *
     * @throws Exception\ParserException
     */
    public function parse($money, $forceCurrency = null)
    {
        $data = json_decode($money, true);
        if (is_array($data) && isset($data['amount']) && isset($data['currency'])) {
            return new Money($data['amount'], new Currency($data['currency']));
        }
        throw new Exception\ParserException('Incorrect json');
    }

    /**
     * Formats a Money object as string.
     *
     * @param Money $money
     *
     * @return string
     *
     * Exception\FormatterException
     */
    public function format(Money $money)
    {
        return json_encode($money);
    }
}