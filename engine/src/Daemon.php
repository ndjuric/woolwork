<?php

namespace Woolworks\Engine;

use \swoole_process;
use Woolworks\Engine\SystemModule\System;
use Woolworks\Engine\SystemModule\Config;
use Woolworks\Engine\SystemModule\Logger;

abstract class Daemon
{
    private $pid = 0;
    protected $config;
    protected $logger;

    public function __clone()
    {
        return null;
    }

    public function __construct($config)
    {
        Config::setConfig($config);
        $this->config = Config::getConfig();
        $this->logger = new Logger();
        $this->logger->setFile($this->config['log_file']);
        $this->logger->setAppend(true);
    }

    private function concurrent_control(): array
    {
        if ($pid = System::get_pid_from_file($this->config['pid_file'])) {
            if (swoole_process::kill($pid, 0)) {
                return ['status' => false, 'message' => "Failed. Process is already running with pid:{$pid})\n"];
            }
        }

        if (!System::write_pid_file($this->config['pid_file'], $this->pid)) {
            return ['status' => false, 'message' => "Unable to write to pidfile.\n"];
        }

        return ['status' => true, 'message' => "Started!\n"];
    }

    private function register_signal_handler(): void
    {
        swoole_process::signal(SIGTERM, function ($signo) {
            $this->logger->log("Caught a SIGTERM({$signo}) signal\n", $this->config['log_file']);
            System::remove_file($this->config['pid_file']);
            swoole_process::kill($this->pid, SIGKILL);
        });
    }

    private function check_environment(): void
    {
        if (!System::make_dir($this->config['log_dir'])) {
            exit();
        }

        if (!function_exists("swoole_set_process_name")) {
            $this->logger->warning("Swoole is not installed!");
            exit();
        }
    }

    /** API */
    public function start(): void
    {
        $this->check_environment();

        swoole_process::daemon();
        $this->pid = posix_getpid();

        $this->logger->log("Starting...\n");
        $validation = $this->concurrent_control();
        echo $validation['message'];
        $this->logger->log($validation['message'], $this->config['log_file']);
        if (!$validation['status']) {
            exit(0);
        }

        swoole_set_process_name($this->config['proc_name']);

        $this->register_signal_handler();
        $this->run();
    }

    public function stop(): bool
    {
        $pid_file = $this->config['pid_file'];

        if ($pid = System::get_pid_from_file($pid_file)) {
            swoole_process::kill($pid, SIGTERM);
            return true;
        }

        echo "{$this->config['proc_name']} is not running.\n";
        return false;
    }

    public function reload(): void
    {
        $pid_file = $this->config['pid_file'];
        $pid = System::get_pid_from_file($pid_file);
        swoole_process::kill($pid, SIGUSR1);
    }

    public function restart(): void
    {
        $this->stop();
        $this->start();
    }

    /** END API */

    abstract public function run();
}
