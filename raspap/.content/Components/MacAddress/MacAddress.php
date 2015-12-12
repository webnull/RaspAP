<?php
namespace RaspAP\Components\MacAddress;

use Panthera\Components\Orm\ORMBaseFrameworkObject;

/**
 * RaspAP
 * --
 * Class MacAddress
 *
 * @package RaspAP\Components\MacAddress
 */
class MacAddress extends ORMBaseFrameworkObject
{
    protected static $__orm_Table = 'mac_address';
    protected static $__orm_IdColumn = 'id';

    /**
     * @orm
     * @column id
     * @var integer
     */
    protected $macId = null;

    /**
     * @orm
     * @column mac
     * @var string
     */
    protected $macAddress = '';

    /**
     * @orm
     * @column title
     * @var string
     */
    protected $macTitle = '';

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->macTitle;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->macTitle = $title;
        return $this;
    }

    /**
     * @param string $mac
     * @return $this
     */
    public function setMacAddress($mac)
    {
        if (!preg_match('/([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)/i', $mac))
        {
            throw new \InvalidArgumentException('Invalid MAC Address specified', 'INVALID_MAC_ADDRESS');
        }

        $this->macAddress = $mac;
        return $this;
    }
}