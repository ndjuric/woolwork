<?php

namespace Woolworks\Engine\WebSocketModule;

use Woolworks\Engine\SystemModule\Config;
use Woolworks\Engine\SystemModule\Logger;

class WebSocketServer
{
    private $config;

    private $logger;

    private $server;

    private $clients = [];

    public function __construct($config)
    {
        Config::setConfig($config);
        $this->config = Config::getConfig();
        $this->logger = new Logger();
        $this->logger->setFile($this->config['log_file']);
        $this->logger->setAppend(true);
    }

    public function run()
    {
        $this->server = new \Swoole\WebSocket\Server("0.0.0.0", 2345, SWOOLE_BASE);
        $this->server->set([
            'daemonize' => true,
            'pid_file' => $this->config['pid_file'],
        ]);

        $this->server->on('Start', function(\Swoole\Websocket\Server $server) {
            swoole_set_process_name($this->config['proc_name']);
        });

        $this->server->on('open', function (\Swoole\Websocket\Server $server, $req) {
            $this->clients[$req->fd] = true;
            $this->logger->log("OPEN: {$req->fd}\n");
        });

        $this->server->on('message', function ($server, \Swoole\Websocket\Frame $frame) {
            $this->logger->log("RECV: {$frame->data}\n");

            /**  @var \Swoole\WebSocket\Server $server */
            foreach ($this->clients as $client_fd => $status) {
                $server->push($client_fd, $frame->data);
            }
        });

        $this->server->on('close', function ($server, $fd) {
            $this->logger->log("CLOSE: {$fd}\n");
            unset($this->clients[$fd]);
        });

        $this->server->start();
    }
}