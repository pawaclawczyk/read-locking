<?php

ini_set('date.timezone', 'UTC');

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Process\Process;

$p1 = new Process('php select_for_update.php 2');
$p2 = new Process('php select_for_update.php 3');
$p3 = new Process('php select_for_update.php 5');

$p1->start();
$p2->start();
$p3->start();

while ($p1->isRunning() || $p2->isRunning() || $p3->isRunning()) {
    sleep(1);
}

$data = [];


$lines = explode(PHP_EOL, $p1->getOutput());

foreach ($lines as $line) {
    list($usec, $text) = explode('###', $line);
    $data[$usec] = 'P1: '.$text;
}

$lines = explode(PHP_EOL, $p2->getOutput());

foreach ($lines as $line) {
    list($usec, $text) = explode('###', $line);
    $data[$usec] = 'P2: '.$text;
}

$lines = explode(PHP_EOL, $p3->getOutput());

foreach ($lines as $line) {
    list($usec, $text) = explode('###', $line);
    $data[$usec] = 'P3: '.$text;
}

ksort($data);

foreach ($data as $key => $value) {
    if (empty($key)) {
        continue;
    }
    echo $key. ' '.$value.PHP_EOL;
}
