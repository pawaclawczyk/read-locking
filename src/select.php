<?php

ini_set('date.timezone', 'Europe/Warsaw');

require_once __DIR__.'/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$connection = DriverManager::getConnection([
    'dbname' => 'test',
    'user' => 'root',
    'password' => 'test',
    'host' => 'db',
    'driver' => 'pdo_mysql',
]);

$records = $connection->executeQuery('SELECT * FROM test')->fetchAll();
var_dump($records);