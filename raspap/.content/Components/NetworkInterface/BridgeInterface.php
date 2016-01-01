<?php
namespace RaspAP\Components\NetworkInterface;

/**
 * RaspAP
 * --
 * BridgeInterface
 *
 * @package RaspAP\Components\NetworkInterface
 */
class BridgeInterface extends WiredInterface
{
    /**
     * @return array
     */
    public function getPossibleRoles()
    {
        return [

        ];
    }

    /**
     * @return bool
     */
    public function canBeUsedInBridge()
    {
        return false;
    }
}