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

$connection->exec('DROP TABLE IF EXISTS select_for_update');

$connection->exec(
<<<SQL
CREATE TABLE select_for_update (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	processing SMALLINT NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
);
SQL
);

for ($i = 0; $i < 10; $i++) {
    $connection->insert('select_for_update', []);
}

$result = $connection->executeQuery('SELECT * FROM select_for_update')->fetchAll();

var_dump($result);
