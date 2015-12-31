<?php
namespace RaspAP\Components\TOR;

use Panthera\Classes\BaseExceptions\InvalidArgumentException;
use Panthera\Components\Orm\ORMBaseFrameworkObject;
use RaspAP\Components\NetworkInterface\AbstractInterface;
use RaspAP\Components\NetworkInterface\Helpers\InterfaceDaemons;
use Panthera\Components\Kernel\Framework;

/**
 * RaspAP
 * --
 * TorEntity represents TOR configuration in "interfaces" table
 * Configuration is stored in an entry as "tor0" dummy interface
 *
 * @package RaspAP\Components\TOR
 */
class TorEntity extends AbstractInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $data = Framework::getInstance()->database->query('SELECT * FROM interfaces WHERE name = :name', [
            'name' => 'tor0',
        ]);

        if ($data && isset($data[0]))
        {
            ORMBaseFrameworkObject::__construct($data[0]);
        }
        else
        {
            ORMBaseFrameworkObject::__construct(null);
        }

        $this->interfaceName = 'tor0';
        $this->daemonsList = new InterfaceDaemons($this->interfaceDaemons);
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function parse()
    {
        return false;
    }

    /**
     * Enable TOR networking
     *
     * @param string $mode ['relay', 'bridge', 'exit']
     * @throws InvalidArgumentException
     * @return $this
     */
    public function enable($mode)
    {
        if (!in_array($mode, ['relay', 'bridge', 'exit']))
        {
            throw new InvalidArgumentException('Mode must be "relay", "bridge" or "exit"', 'TOR_INVALID_MODE');
        }

        $this->getDaemons()->put('tor', $mode);
        return $this;
    }

    /**
     * Disable TOR
     *
     * @return $this
     */
    public function disable()
    {
        $this->getDaemons()->clear();
        return $this;
    }

    /**
     * @return bool
     */
    public function isProxyEnabled()
    {
        return (bool)intval($this->getDaemons()->get('privoxy'));
    }

    /**
     * @return string
     */
    public function isEnabled()
    {
        return $this->getDaemons()->get('tor');
    }
}