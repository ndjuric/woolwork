<?php

namespace Woolworks\Engine\WorkerModule;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Woolworks\Engine\Daemon;
use Woolworks\Engine\WebSocketModule\WebSocketClient;

class Worker extends Daemon
{
    private $connection;
    private $channel;

    public function __construct($config)
    {
        parent::__construct($config);
    }

    function run()
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['queue']['host'],
            $this->config['queue']['port'],
            $this->config['queue']['user'],
            $this->config['queue']['pass']
        );
        $this->channel = $this->connection->channel();

        $this->channel->queue_declare($this->config['queue']['name'], false, true, false, false);

        $this->logger->log("[*] Worker started, waiting for messages... To exit press CTRL+C\n");

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(
            $this->config['queue']['name'],
            '',
            false,
            false,
            false,
            false,
            [$this, 'process_message']
        );

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }

    public function process_message(AMQPMessage $msg)
    {
        $this->logger->log($msg->body);
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        $this->publish($msg->body);
    }

    private function publish($message)
    {
        go(function () use (&$message) {
            $client = new WebSocketClient('0.0.0.0', 2345);
            $client->connect();
            $client->send(json_encode($message));
            $client->close();
        });
    }
}
