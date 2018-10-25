<?php

namespace Woolworks\Engine\SystemModule;

class System
{
    public static function check_writable($file)
    {
        return (self::check_file_exists($file) || is_dir($file)) && is_writable($file);
    }

    public static function check_file_exists($file)
    {
        clearstatcache();
        return is_file($file);
    }

    public static function get_file_contents($file)
    {
        if (!self::check_file_exists($file)) {
            return false;
        }
        return file_get_contents($file);
    }

    public static function put_contents_to_file($file, $data)
    {
        if (!self::check_file_exists($file)) {
            if (!self::make_dir(dirname($file))) {
                return false;
            }
        }
        return file_put_contents($file, $data, LOCK_EX);
    }

    public static function make_dir($dir)
    {
        if (is_dir($dir)) {
            return true;
        }
        $parent_dir = dirname($dir);
        if (is_dir($parent_dir)) {
            if (self::check_writable($parent_dir)) {
                return mkdir($dir);
            }
            trigger_error("{$dir} is unwritable!", E_USER_WARNING);
            return false;

        }

        return self::make_dir($parent_dir);
    }

    public static function get_pid_from_file($file)
    {
        return self::get_file_contents($file);
    }

    public static function write_pid_file($file, $pid)
    {
        if ($pid === null) {
            return touch($file);
        }
        return self::put_contents_to_file($file, $pid);
    }

    public static function create_file($file)
    {
        return touch($file);
    }

    public static function remove_file($file)
    {
        if (!self::check_file_exists($file)) {
            return true;
        }
        return unlink($file);
    }
}
