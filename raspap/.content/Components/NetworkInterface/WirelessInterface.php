<?php
namespace RaspAP\Components\NetworkInterface;
use RaspAP\Components\HostAPD\HostAPDInterface;
use RaspAP\Components\HostAPD\PSKCollection;

/**
 * RaspAP
 * --
 * WirelessInterface
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 * @package RaspAP\Components\NetworkInterface
 */
class WirelessInterface extends WiredInterface
{
    /** @var string $name */
    protected $name = '';

    /** @var string $output */
    protected $output = '';

    /** @var HostAPDInterface $hostAPD */
    protected $hostAPD;

    /** @var PSKCollection $psk */
    protected $psk;

    /**
     * Parse output of "ifconfig" and "iwconfig"
     */
    public function parse()
    {
        parent::parse();
        $this->parseWirelessOutput();
    }

    /**
     * Checks if wireless interface is an Access Point
     *
     * @return bool
     */
    public function isAccessPoint()
    {
        return isset($this->details['Mode']) && $this->details['Mode'] == 'Master';
    }

    /**
     * Check if interface is active
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->isAccessPoint() || (
            isset($this->details['LinkQuality']) && $this->details['LinkQuality'] &&
            isset($this->details['IPv4']) && $this->details['IPv4']
        );
    }

    /**
     * Parse "iwconfig" output
     */
    protected function parseWirelessOutput()
    {
        // ESSID
        $this->extractData('/ESSID:\"(.*)\"/i', 'ESSID', 1);

        // Mode
        $this->extractData('/Mode:([A-Za-z]+)/i', 'Mode', 1);

        // Frequency
        $this->extractData('/Frequency:([0-9\.]+)/i', 'Frequency', 1);

        // Mac address of AP interface is connected to
        $this->extractData('/Access point:\"(.*)\"/i', 'ConnectedAP_Mac', 1);

        // Standard
        $this->extractData('#IEEE ([0-9\.bgna ]+)#', 'Standard', 1);

        // Transmission power
        $this->extractData('#Tx\-Power\=([0-9]+)#', 'TransmissionPower', 1);

        // Link Quality
        $this->extractData('/Link Quality\=([0-9\/]+) /i', 'LinkQuality', 1);

        // Signal level
        $this->extractData('/Signal level\=([0-9\-]+) /i', 'SignalLevel', 1);
    }

    /**
     * Get list of Mac Addresses and passwords for WPA2 Enterprise authorization
     *
     * @return PSKCollection
     */
    public function getWPAPassphrases()
    {
        if (!$this->psk instanceof PSKCollection)
        {
            $this->psk = new PSKCollection($this);
        }

        return $this->psk;
    }

    /**
     * Returns plaintext password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->getHostAPD()->getPassword();
    }

    /**
     * @return array
     */
    public function getPossibleRoles()
    {
        return [
            'access_point',
            'monitor',
            'down',
        ];
    }

    /**
     * @return HostAPDInterface
     */
    public function getHostAPD()
    {
        if (!$this->hostAPD)
        {
            $this->hostAPD = new HostAPDInterface($this);
        }

        return $this->hostAPD;
    }

    /**
     * Wireless interface cannot be joined using a brctl, but could be joined using hostapd
     *
     * @return bool
     */
    public function canBeUsedInBridge()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isBridgeConnected()
    {
        if ($this->getHostAPD()->isCreatingABridge())
        {
            return false;
        }

        return parent::isBridgeConnected();
    }
}