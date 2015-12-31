<?php
namespace RaspAP\Classes;

/**
 * RaspAP
 * --
 * Class DaemonUtils
 *
 * @package RaspAP\Classes
 */
class DaemonUtils
{
    /**
     * Check if daemon is running
     *
     * @return bool
     */
    public static function isTurnedOn()
    {
        return true;
        //return strlen(shell_exec('ps x |grep "raspapd" | grep -v "grep"')) > 0;
    }
}