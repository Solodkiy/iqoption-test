<?php

namespace IqOptionTest\Command\Test;


use IqOptionTest\Transport\Envelope;
use IqOptionTest\Operation\DrawOperation;
use IqOptionTest\OperationSerializer;
use IqOptionTest\OperationsProcessor;
use IqOptionTest\Transport\Queue;
use IqOptionTest\QueueManager;
use Money\Currency;
use Money\Money;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DrawTestCommand extends AbstractTestCommand
{

    protected function configure()
    {
        $this->setName('test:draw')
             ->addArgument('account_id', InputArgument::REQUIRED)
             ->addArgument('amount', InputArgument::REQUIRED);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountId = $input->getArgument('account_id');
        $amount = new Money($input->getArgument('amount'), new Currency('USD'));

        $operation = new DrawOperation($accountId, $amount);
        $this->processOperation($operation, $output);
    }


}