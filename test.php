<?php

include('./vendor/autoload.php');

use Workerman\Worker;

$worker = new Worker("websocket://0.0.0.0:2346");

$worker->count = 4;

$worker->onConnect = function ($connection) {
    echo "New Connection\n";
};

$worker->onMessage = function ($connection, $data) {
    $connection->send('Hello '.$data);
};

$worker->onClose = function ($connection) {
    echo "Connection Closed\n";
};

Worker::runAll();