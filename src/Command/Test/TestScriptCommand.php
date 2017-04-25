<?php


namespace IqOptionTest\Command\Test;


use IqOptionTest\Operation\DepositOperation;
use IqOptionTest\Operation\DrawOperation;
use IqOptionTest\Operation\GetBalanceOperation;
use IqOptionTest\Operation\TransferOperation;
use Money\Currency;
use Money\Money;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestScriptCommand extends AbstractTestCommand
{
    protected function configure()
    {
        $this->setName('test:script');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $operation = $this->generateRandomOperation();

            $output->writeln('Run '.get_class($operation).' '.json_encode($operation->toArray()));
            $this->processOperation($operation, $output);
            sleep(1);
        }
    }

    private function generateRandomOperation()
    {
        $operationType = rand(1, 4);
        switch ($operationType) {
            case 1: return new DrawOperation($this->generateAccountId(), $this->generateMoney());
            case 2: return new DepositOperation($this->generateAccountId(), $this->generateMoney());
            case 3: return new TransferOperation($this->generateAccountId(), $this->generateAccountId(), $this->generateMoney());
            case 4: return new GetBalanceOperation($this->generateAccountId());
        }
    }

    private function generateAccountId(): int
    {
        return rand(1, 10);
    }

    private function generateMoney(): Money
    {
        return new Money(rand(1, 1000), new Currency('USD'));
    }


}