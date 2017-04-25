<?php


namespace IqOptionTest\Command\Test;



use IqOptionTest\Factory;
use IqOptionTest\Message\ErrorResponse;
use IqOptionTest\Message\MessageInterface;
use IqOptionTest\Message\SuccessResponse;
use IqOptionTest\Operation\AbstractOperation;
use IqOptionTest\Message\BalanceStatus;
use IqOptionTest\Transport\TestCommandProcessor;
use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractTestCommand extends Command
{

    protected function processOperation(AbstractOperation $operation, OutputInterface $output)
    {
        $message = $this->getProcessor()->processOperation($operation);
        $this->outputResponse($message, $output);
    }

    private function getProcessor(): TestCommandProcessor
    {
        return memorize(function () {
            return new TestCommandProcessor(Factory::getRedisClient());
        });
    }

    protected function outputResponse(?MessageInterface $message, OutputInterface $output)
    {
        if (is_null($message)) {
            $output->writeln('Answer timeout!');
        } elseif ($message instanceof BalanceStatus) {
            $output->writeln('Balance: '.$message->getBalance()->getAmount());
        } elseif ($message instanceof ErrorResponse) {
            $output->writeln($message->getErrorText());
        } elseif ($message instanceof SuccessResponse) {
            $output->writeln('OK');
        } else {
            $output->writeln('Message unknown');
        }

    }
}