<?php
namespace RaspAP\Classes;

/**
 * RaspAP
 * --
 * IP Address related functions
 *
 * @package RaspAP\Classes
 */
class IP
{
    /**
     * IP Address validator
     *
     * @param string $address
     * @param array $attributes
     * @return string
     */
    public static function addressValidator($address, $attributes)
    {
        return filter_var($address, FILTER_VALIDATE_IP) ? true : 'Not a valid IP address';
    }
}