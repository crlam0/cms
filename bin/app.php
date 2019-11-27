#!/usr/bin/env php

<?php
require __DIR__.'/../include/config/config.local.php';
require __DIR__.'/../include/config/misc.php';
$DIR = __DIR__.'/../';

if(file_exists($DIR.'vendor/autoload.php')) {
    require_once $DIR.'vendor/autoload.php';
} else {
    die('Cant find autoloader');
}

use Classes\App;
use Symfony\Component\Console\Application;
use Phinx\Console\Command;

$App = new App($DIR, $SUBDIR);
$App->connectDB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);

require_once $DIR__.'include/lib_functions.php';

$cli = new Application('Application console');
$cli->add(new Classes\Command\CacheClearCommand($console['cachePaths']));
$cli->add(new Classes\Command\DBDumpCommand());
$cli->add(new Classes\Command\DBRestoreCommand());
$cli->add(new Classes\Command\DBDumpClearCommand());
$cli->addCommands([
    new Command\Create(),
    new Command\Migrate(),
    new Command\Rollback(),
    new Command\Status(),
]);
$cli->run();

