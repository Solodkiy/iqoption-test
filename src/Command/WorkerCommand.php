<?php

namespace IqOptionTest\Command;


use IqOptionTest\DatabaseGateway;
use IqOptionTest\Factory;
use IqOptionTest\Transport\Envelope;
use IqOptionTest\Message\BalanceStatus;
use IqOptionTest\Operation\AbstractOperation;
use IqOptionTest\Operation\GetBalanceOperation;
use IqOptionTest\OperationController;
use IqOptionTest\OperationProcessor\GetBalanceProcessor;
use IqOptionTest\OperationSerializer;
use IqOptionTest\Transport\Queue;
use Money\Currency;
use Money\Money;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->logger = new NullLogger();
    }


    protected function configure()
    {
        $this->setName('worker');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = new ConsoleLogger($output);

        $queue = new Queue('work', Factory::getRedisClient());
        $controller = new OperationController(new DatabaseGateway(Factory::getRedisClient()));

        while (true) {
            try {
                $envelope = $queue->getMessageWithBlock(10);
                if ($envelope) {

                    $answerQueueName = $envelope->getAnswerTo();
                    $answerQueue = new Queue($answerQueueName, Factory::getRedisClient());

                    $message = $envelope->getMessage();
                    $answer = null;
                    if ($message instanceof AbstractOperation) {
                        $this->logger->debug('process '.get_class($message));
                        $answer = $controller->processOperation($message);
                    }

                    if ($answer) {
                        $answerQueue->push(Envelope::create($answer, 'work'));
                    }
                    $queue->acknowledge($envelope);
                }
            } catch (\Exception $e) {
                $this->logger->error('Error: '.$e);
            }

        }
    }


}