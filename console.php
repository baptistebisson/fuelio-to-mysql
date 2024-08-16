#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\FuelioToMysqlCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$application = new Application();

$application->add(new FuelioToMysqlCommand());

$application->run();
