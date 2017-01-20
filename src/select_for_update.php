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

$modulo = $argv[1];

$connection = DriverManager::getConnection([
    'dbname' => 'test',
    'user' => 'root',
    'password' => 'test',
    'host' => 'db',
    'driver' => 'pdo_mysql',
]);

$connection->setTransactionIsolation(Connection::TRANSACTION_READ_UNCOMMITTED);

$connection->setAutoCommit(true);

$connection->beginTransaction();

$print('Before select');
$records = $connection->executeQuery('SELECT * FROM select_for_update WHERE processing = :notProcessing FOR UPDATE', ['notProcessing' => 0])->fetchAll();
$print('After select');

//sleep(10);

$processedIds = [];

foreach ($records as $record) {
    if (0 !== $record['id'] % $modulo) {
        continue;
    }

    $connection->update('select_for_update', ['processing' => 1], ['id' => $record['id']]);

    $print(sprintf('Record %d was updated', $record['id']));

    $processedIds[] = $record['id'];

    $connection->insert('select_for_update', ['processing' => 0]);

    $print(sprintf('Record %d was inserted', $connection->lastInsertId()));

}

$print('Processed ids: '.implode(', ', $processedIds));

$connection->commit();
