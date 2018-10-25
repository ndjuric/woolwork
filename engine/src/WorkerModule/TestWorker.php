<?php

namespace Woolworks\Engine\WorkerModule;

use Woolworks\Engine\SystemModule\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TestWorker
{
    private $argv;

    private $config;

    private $connection;

    private $channel;

    public function __construct($config, $argv)
    {
        Config::setConfig($config);
        $this->config = Config::getConfig();

        $this->argv = $argv;
    }

    public function run()
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['queue']['host'],
            $this->config['queue']['port'],
            $this->config['queue']['user'],
            $this->config['queue']['pass']
        );
        $this->channel = $this->connection->channel();

        $this->channel->queue_declare($this->config['queue']['name'], false, true, false, false);

        $data = implode(' ', array_slice($this->argv, 1));
        if (empty($data)) {
            $data = "TEST DATA";
        }
        $msg = new AMQPMessage($data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );

        $this->channel->basic_publish($msg, '', $this->config['queue']['name']);

        echo " [x] Sent ", $data, "\n";

        $this->channel->close();
        $this->connection->close();
    }
}

