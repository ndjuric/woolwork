<?php

require_once($autoloaderPath = realpath(__DIR__ . '/../vendor/autoload.php'));

function main()
{
    $serv = new swoole_server('127.0.0.1', 9501);


    $serv->on('connect', function ($serv, $fd) {
        echo "Client:Connect.\n";
    });

    $serv->on('receive', function ($serv, $fd, $from_id, $data) {
        $serv->send($fd, $data);
    });

    $serv->on('close', function ($serv, $fd) {
        echo "Client: Close.\n";
    });


    $serv->start();

}

main();