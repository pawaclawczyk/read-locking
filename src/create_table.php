<?php

ini_set('date.timezone', 'UTC');

require_once __DIR__.'/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$connection = DriverManager::getConnection([
    'dbname' => 'test',
    'user' => 'root',
    'password' => 'test',
    'host' => 'db',
    'driver' => 'pdo_mysql',
]);

$result = $connection->exec('DROP TABLE IF EXISTS test');

var_dump($result);

$result = $connection->exec(
<<<SQL
CREATE TABLE test (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	ip VARCHAR(100) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
);
SQL
);

var_dump($result);
