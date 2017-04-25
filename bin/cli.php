#!/bin/env php
<?php

use IqOptionTest\Command\Test\DepositTestCommand;
use IqOptionTest\Command\Test\DrawTestCommand;
use IqOptionTest\Command\Test\GetBalanceTestCommand;
use IqOptionTest\Command\Test\TestScriptCommand;
use IqOptionTest\Command\Test\TransferTestCommand;
use IqOptionTest\Command\WorkerCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/../vendor/autoload.php';

$application = new Application();
$application->add(new WorkerCommand());
$application->addCommands([
    new DrawTestCommand(),
    new DepositTestCommand(),
    new GetBalanceTestCommand(),
    new TransferTestCommand(),
    new TestScriptCommand()
]);
$application->run();