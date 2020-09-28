#!/usr/bin/env php

<?php
require __DIR__.'/../local/config.php';
require __DIR__.'/../include/config/misc.php';
$DIR = __DIR__.'/../';

if(file_exists($DIR.'vendor/autoload.php')) {
    require_once $DIR.'vendor/autoload.php';
} else {
    die('Cant find autoloader');
}

use Symfony\Component\Console\Application;
use Phinx\Console\Command;

require_once __DIR__ . '/../include/lib_functions.php';

$cli = new Application('Application console');
$cli->add(new classes\commands\CacheClearCommand($console['cachePaths']));
$cli->add(new classes\commands\DBDumpCommand());
$cli->add(new classes\commands\DBRestoreCommand());
$cli->add(new classes\commands\DBDumpClearCommand());
$cli->addCommands([
    new Command\Create(),
    new Command\Migrate(),
    new Command\Rollback(),
    new Command\Status(),
]);
$cli->run();

