<?php
/**
 * Configuration for application skeleton based on Panthera Framework 2
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 */
defined('PF2_NAMESPACE') ?: define('PF2_NAMESPACE', 'RaspAP');

if (!function_exists('ab'))
{
    function ab($a, $b)
    {
        return $a ? $a : $b;
    }
}

$defaultConfig = [
    'developerMode' => true,
    'enabledPackages' => [ 'ManagementDashboard' ],
    'Routing' => [
        'rootPath' => '/',
    ],

    'database' => [

        // sqlite
        'type' => 'SQLite3',
        'name' => 'database',

        // read-write user
        'host'     => null,
        'user'     => null,
        'password' => null,

        // read-only user
        'readOnlyUser'     => null,
        'readOnlyPassword' => null,

        'charset'          => 'utf-8',
    ],

    'application' => [
        'name'          => 'RaspAP',
        'repository'    => 'https://github.com/webnull/RaspAP',
        'repositoryKey' => '',
    ],

    'SudoUsers' => [ 'root', 'your-user-here' ],
];

// if defined PHPUnit, initialize Panthera Framework 2 once again for testing purposes
require_once __VENDOR_PATH__ . '/pantheraframework/panthera/lib/init.php';
