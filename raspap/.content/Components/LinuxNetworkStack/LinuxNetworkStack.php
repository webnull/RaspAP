<?php
namespace RaspAP\Components\LinuxNetworkStack;

use Guzzle\Service\Exception\ValidationException;
use Panthera\Components\Validator\Validator;
use RaspAP\Classes\AbstractConfigClass;
use RaspAP\Components\NetworkInterface\AbstractInterface;
use RaspAP\Components\NetworkInterface\WirelessInterface;

/**
 * RaspAP
 * --
 * LinuxNetworkStack
 *
 * @package RaspAP\Components\LinuxNetworkStack
 */
class LinuxNetworkStack extends AbstractConfigClass
{
    /** @var array $allowedPacketTypes */
    protected $allowedPacketTypes = [
        'TCP', 'UDP', 'ICMP', 'Broadcast', 'Multicast', 'ARP',
    ];

    /**
     * Constructor
     *
     * @param WirelessInterface $interface
     */
    public function __construct(AbstractInterface $interface)
    {
        parent::__construct($interface);

        if ($interface->getDaemons()->get('networkStack'))
        {
            $this->data = $interface->getDaemons()->get('networkStack');
        }
    }

    /**
     * Sets the interface in down mode
     */
    public function down()
    {
        $this->data = [];
        $this->set('down_interface', true);
    }

    /**
     * @return bool
     */
    public function isDown()
    {
        return $this->get('down_interface', false);
    }

    /**
     * Decide if we are touching this interface or not
     *
     * @param bool $value
     * @return $this
     */
    public function useInterface($value = true)
    {
        $this->set('useInterface', (bool)$value);
        return $this;
    }

    /**
     * @return bool
     */
    public function isUsed()
    {
        return $this->get('useInterface', false);
    }

    /**
     * @param string $filter
     * @param array $packetTypes
     * @param int $packetSize
     * @param bool $setupInterface
     */
    public function setupMonitorMode($filter = '', $packetTypes = ['UDP', 'TCP'], $packetSize = 68, $setupInterface = true)
    {
        $this->clearKeys([
            'down_interface',
        ]);

        // validate and filter $packetTypes
        if (is_array($packetTypes) && $packetTypes)
        {
            foreach ($packetTypes as $key => $type)
            {
                if (!in_array($type, $this->allowedPacketTypes))
                {
                    unset($packetTypes[$key]);
                }
            }
        }

        $this->set('monitor_setupInterface', (bool)$setupInterface);
        $this->set('monitor_packetSize', (int)$packetSize);
        $this->set('monitor_packetTypes', (array)$packetTypes);
        $this->set('monitor_filter', $filter);
    }

    /**
     * Check if interface is in monitoring mode state
     *
     * @return bool
     */
    public function isMonitoring()
    {
        return $this->get('monitor_packetTypes') || $this->get('monitor_filter');
    }

    /**
     * Check if interface is setup to monitoring mode (iwconfig {interface} mode monitor)
     * or just monitoring in current connected network
     *
     * @return bool
     */
    public function isMonitorModeSetup()
    {
        return (bool)$this->get('monitor_setupInterface');
    }

    /**
     * @return int
     */
    public function getPacketSize()
    {
        return (int)$this->get('monitor_packetSize');
    }

    /**
     * @return array
     */
    public function getPacketTypes()
    {
        return (array)$this->get('monitor_packetTypes');
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return (string)$this->get('monitor_filter');
    }

    /**
     * Setup interface as DHCP Client, so it will try to connect to a dhcp server to get an IP Address
     */
    public function setupAsDHCPClient()
    {
        $this->clearKeys();
        $this->set('client_dhcp', true);
    }

    /**
     * Setup as static client to connect to a network using this interface
     *
     * @param string $ip
     * @param string $netmask
     * @param string $gateway
     * @param string $broadcast
     *
     * @throws \Panthera\Classes\BaseExceptions\ValidationException
     * @return $this
     */
    public function setupAsStaticClient($ip, $netmask = null, $gateway = null, $broadcast = null)
    {
        $this->clearKeys();
        $this->set('client_static', true);

        if (!$ip || !Validator::validate($ip, 'Classes/IP::address'))
        {
            throw new ValidationException('Invalid IP Address specified', 'NETWORK_STACK_INVALID_IP');
        }

        $this->set('address', $ip);
        Validator::validate($netmask, 'Classes/IP::address') ? $this->set('netmask', $netmask) : null;
        Validator::validate($gateway, 'Classes/IP::address') ? $this->set('gateway', $gateway) : null;
        Validator::validate($broadcast, 'Classes/IP::address') ? $this->set('broadcast', $broadcast) : null;

        return $this;
    }

    /**
     * @return string
     */
    public function getIPAddress()
    {
        return $this->get('address');
    }

    /**
     * @return string
     */
    public function getGatewayAddress()
    {
        return $this->get('gateway');
    }

    /**
     * @return string
     */
    public function getBroadcastAddress()
    {
        return $this->get('broadcast');
    }

    /**
     * @return string
     */
    public function getNetmaskAddress()
    {
        return $this->get('netmask');
    }

    /**
     * @return bool
     */
    public function isStaticClient()
    {
        return (bool)$this->get('client_static');
    }

    /**
     * @return bool
     */
    public function isDHCPClient()
    {
        return (bool)$this->get('client_dhcp');
    }

    /**
     * Save LinuxNetworkStack settings and network interface entity
     */
    public function save()
    {
        if ($this->isDown())
        {
            $this->interface->setRole('down');
        }
        elseif ($this->isMonitoring())
        {
            $this->interface->setRole('monitor');
        }
        elseif ($this->isDHCPClient())
        {
            $this->interface->setRole('client_cable_dhcp');
        }
        elseif ($this->isStaticClient())
        {
            $this->interface->setRole('client_cable_static');
        }

        $this->interface->getDaemons()->clear();
        $this->interface->getDaemons()->put('networkStack', $this->data);
        $this->interface->save();
    }
}