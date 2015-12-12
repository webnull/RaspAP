<?php
namespace RaspAP\Components\LinuxNetworkStack;

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
        $this->set('downInterface', true);
    }

    /**
     * @return bool
     */
    public function isDown()
    {
        return $this->get('downInterface', false);
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
            'downInterface',
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

        $this->interface->getDaemons()->clear();
        $this->interface->getDaemons()->put('networkStack', $this->data);
        $this->interface->save();
    }
}