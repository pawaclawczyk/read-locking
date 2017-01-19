<?php

ini_set('date.timezone', 'UTC');
require_once __DIR__.'/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Carbon\Carbon;

$print = function ($info) {
    list($usec, $sec) = explode(' ', microtime());
    $usec = substr($usec, 2);
    echo sprintf('%s%s###%s%s', $sec, $usec, $info, PHP_EOL);
};

$connection = DriverManager::getConnection([
    'dbname' => 'test',
    'user' => 'root',
    'password' => 'test',
    'host' => 'db',
    'driver' => 'pdo_mysql',
]);

$ip = $argv[1];

$print('Start');

$connection->setTransactionIsolation(Connection::TRANSACTION_READ_UNCOMMITTED);

//$connection->setAutoCommit(false);

//$connection->executeQuery('LOCK TABLES test WRITE');
//$print('Locked');

$connection->beginTransaction();
$print('Transaction begin');

$records = $connection->executeQuery('SELECT * FROM test WHERE created_at > :threeMinutesAgo FOR UPDATE', ['threeMinutesAgo' => Carbon::now()->subMinutes(3)])->fetchAll();
//$records = $connection->executeQuery('SELECT * FROM test')->fetchAll();
$print('Selected and locked, count '.count($records));

foreach ($records as $record) {
    if ($ip === $record['ip']) {
        $print('IP exists');
        exit;
    }
}

$seconds = rand(1, 20);
sleep($seconds);
$print('sleep '.$seconds);

$connection->insert('test', ['ip' => $ip]);
$print('IP inserted');

//$connection->executeQuery('UNLOCK TABLES');
//$print('Unlocked');

$connection->commit();
$print('Transaction commit');