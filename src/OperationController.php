<?php


namespace IqOptionTest;


use IqOptionTest\Message\ErrorResponse;
use IqOptionTest\Message\MessageInterface;
use IqOptionTest\Message\SuccessResponse;
use IqOptionTest\Operation\AbstractOperation;
use IqOptionTest\Message\BalanceStatus;
use IqOptionTest\Operation\DepositOperation;
use IqOptionTest\Operation\DrawOperation;
use IqOptionTest\Operation\GetBalanceOperation;
use IqOptionTest\Operation\TransferOperation;
use Predis\Client;

class OperationController
{
    /**
     * @var DatabaseGateway
     */
    private $databaseGateway;

    /**
     * OperationController constructor.
     * @param DatabaseGateway $databaseGateway
     */
    public function __construct(DatabaseGateway $databaseGateway)
    {
        $this->databaseGateway = $databaseGateway;
    }

    /**
     * @param AbstractOperation $operation
     * @return MessageInterface
     */
    public function processOperation(AbstractOperation $operation)
    {

        try {
            if ($operation instanceof DrawOperation) {
                $this->databaseGateway->draw($operation->getAccountId(), $operation->getAmount(), $operation->getOperationId());
                return new SuccessResponse();
            }
            if ($operation instanceof DepositOperation) {
                $this->databaseGateway->deposit($operation->getAccountId(), $operation->getAmount(), $operation->getOperationId());
                return new SuccessResponse();
            }
            if ($operation instanceof TransferOperation) {
                $this->databaseGateway->transfer($operation->getFromAccountId(), $operation->getToAccountId(), $operation->getAmount(), $operation->getOperationId());
                return new SuccessResponse();
            }

            if ($operation instanceof GetBalanceOperation) {
                $balance = $this->databaseGateway->getBalance($operation->getAccountId());
                return new BalanceStatus($operation->getAccountId(), $balance, BalanceStatus::STATUS_SUCCESS);
            }


        } catch (\RuntimeException $e) {
            return new ErrorResponse(get_class($e).' '.$e->getMessage());

        }

        return new ErrorResponse('Unknown operation');
    }

}