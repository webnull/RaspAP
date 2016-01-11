<?php
namespace RaspAP\Components\HostAPD;

use Panthera\Classes\BaseExceptions\FileException;
use Panthera\Classes\BaseExceptions\InvalidArgumentException;
use RaspAP\Components\MacAddress\MacAddress;
use RaspAP\Components\NetworkInterface\WirelessInterface;

/**
 * RaspAP
 * --
 * Class PSKCollection
 *
 * @package RaspAP\Components\HostAPD
 */
class PSKCollection
{
    /** @var string $configPath */
    protected $configPath = '';

    /** @var string $wirelessName Interface name */
    protected $wirelessName = '';

    /** @var array $data */
    protected $data = [];

    /** @var WirelessInterface $interface */
    protected $interface;

    /** @var HostAPDInterface $hostapd */
    protected $hostapd;

    /**
     * @throws FileException
     * @throws InvalidArgumentException
     * @param WirelessInterface $interface
     */
    public function __construct(WirelessInterface $interface)
    {
        if (!$interface instanceof WirelessInterface)
        {
            throw new InvalidArgumentException('$interface is not a valid interface', 'PSK_COLLECTION_NO_VALID_INTERFACE');
        }

        $this->hostapd = $interface->getDaemons()->get('hostapd');
        $this->interface  = $interface;
        $this->pskCache   = isset($this->hostapd['psk_cache']) ? $this->hostapd['psk_cache'] : [];
        $this->configPath = '/etc/hostapd/raspap/' . $interface->getName() . '.psk';
        $this->loadConfiguration();
    }

    /**
     * Loads configuration from '/etc/hostapd/raspap/%interface%.psk'
     *
     * @throws FileException
     * @return $this
     */
    protected function loadConfiguration()
    {
        if (!is_file($this->configPath))
        {
            if (!is_writable(dirname($this->configPath)))
            {
                throw new FileException('Path "' . dirname($this->configPath) . '" is not writable', 'PSK_PATH_NOT_WRITABLE');
            }

            $fp = fopen($this->configPath, 'w');
            fwrite($fp, '');
            fclose($fp);
        }

        $lines = explode("\n", file_get_contents($this->configPath));

        foreach ($lines as $line)
        {
            $part = explode(' ', $line);

            if (count($part) !== 2)
            {
                continue;
            }

            /** @var MacAddress $mac */
            $mac = MacAddress::fetchOne([
                '|=|mac' => $part[0],
            ]);

            $this->data[$part[0]] = [
                'password' => isset($this->pskCache[$part[1]]) ? $this->pskCache[$part[1]] : '',
                'name'     => $mac instanceof MacAddress ? $mac->getTitle() : '',
                'psk'      => $part[1],
            ];
        }

        return $this;
    }

    /**
     * @param string $macAddress
     * @param string $password
     * @param string $name
     *
     * @throws InvalidArgumentException
     * @return $this
     */
    public function setUser($macAddress, $password, $name)
    {
        if (!preg_match('/([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)\:([A-Za-z0-9]+)/i', $macAddress))
        {
            throw new InvalidArgumentException('Invalid MAC Address specified', 'PSK_INVALID_MAC_ADDRESS');
        }

        // clean up first
        $this->removeUser($macAddress);

        $psk                       = $this->interface->getHostAPD()->generatePassphrase($password);
        $this->pskCache[$psk]      = $password;
        $this->data[$macAddress]   = [
            'password' => $password,
            'name'     => $name,
            'psk'      => $psk,
        ];
        return $this;
    }

    /**
     * @param string $macAddress
     * @return $this
     */
    public function removeUser($macAddress)
    {
        if (isset($this->data[$macAddress]))
        {
            // clean up psk from cache, to do not create a mess after changing password many times
            if (isset($this->pskCache[$this->data[$macAddress]['psk']]))
            {
                unset($this->pskCache[$this->data[$macAddress]['psk']]);
            }

            unset($this->data[$macAddress]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * Save configuration to file
     *
     * @return $this
     */
    public function save()
    {
        $output = "";

        foreach ($this->data as $mac => $passphrase)
        {
            $output .= $mac . " " . $passphrase['psk'] . "\n";

            $object = MacAddress::fetchOne([
                '|=|mac' => $mac,
            ]);

            if (!$object)
            {
                $object = new MacAddress();
                $object->setMacAddress($mac);
            }

            $object->setTitle($passphrase['name']);
            $object->save();
        }

        $fp = fopen($this->configPath, 'w');
        fwrite($fp, $output);
        fclose($fp);

        // save psk cache
        $this->hostapd['psk_cache'] = $this->pskCache;
        $this->interface->getDaemons()->put('hostapd', $this->hostapd, true, true);
        $this->interface->save();

        return $this;
    }
}