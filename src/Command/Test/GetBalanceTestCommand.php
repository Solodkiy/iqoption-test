<?php

namespace IqOptionTest\Command\Test;


use IqOptionTest\Transport\Envelope;
use IqOptionTest\Message\BalanceStatus;
use IqOptionTest\Operation\DrawOperation;
use IqOptionTest\Operation\GetBalanceOperation;
use IqOptionTest\OperationSerializer;
use IqOptionTest\OperationsProcessor;
use IqOptionTest\Transport\Queue;
use IqOptionTest\QueueManager;
use IqOptionTest\Transport\TestCommandProcessor;
use Money\Currency;
use Money\Money;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetBalanceTestCommand extends AbstractTestCommand
{

    protected function configure()
    {
        $this->setName('test:get-balance')
             ->addArgument('account_id', InputArgument::REQUIRED);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountId = $input->getArgument('account_id');

        $operation = new GetBalanceOperation($accountId);
        $this->processOperation($operation, $output);
    }


}