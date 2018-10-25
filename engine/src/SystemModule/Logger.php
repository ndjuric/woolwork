<?php

namespace Woolworks\Engine\SystemModule;

class Logger
{
    private $file = "error.log";
    private $append = false;

    public function __construct()
    {

    }

    public function setFile(string $filename)
    {
        $this->file = $filename;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setAppend(bool $append)
    {
        $this->append = $append;
    }

    public function getAppend(): bool
    {
        return $this->append;
    }

    public function log(string $data, ?string $file = null, ?bool $append = null): bool
    {
        if (is_null($file)) {
            $file = $this->file;
        }

        if (is_null($append)) {
            $append = $this->append;
        }

        if ($append) {
            $data = "[" . date("Y/m/d H:i:s") . "] " . $data;
        }

        if (!System::check_writable($file)) {
            if (!System::check_writable(dirname($file))) {
                trigger_error("File {$file} is unwritable!", E_USER_WARNING);
                return false;
            }
        }

        if (!file_put_contents($file, $data, FILE_APPEND | LOCK_EX)) {
            return false;
        }

        return true;
    }

    public function warning($data)
    {
        $data = "[WARNING] " . $data;
        $this->log($data);
    }
}
