<?php
namespace RaspAP\Components\HostAPD;

use Panthera\Classes\BaseExceptions\ConfigurationException;
use Panthera\Components\Kernel\BaseFrameworkClass;
use RaspAP\Classes\AbstractConfigClass;
use RaspAP\Classes\IniSupport;
use RaspAP\Components\DHCPD\DHCPD;
use RaspAP\Components\NetworkInterface\AbstractInterface;
use RaspAP\Components\NetworkInterface\BridgeInterface;
use RaspAP\Components\NetworkInterface\InterfacesList;
use RaspAP\Components\NetworkInterface\WirelessInterface;

/**
 * RaspAP
 * --
 * hostapd PHP interface
 * Allows reading and generating/saving configuration of hostapd per interface
 *
 * @package RaspAP\Components\HostAPD
 */
class HostAPDInterface extends AbstractConfigClass
{
    /**
     * Location of interface configuration file for hostapd
     *
     * @var string $configPath
     */
    protected $configPath;

    /**
     * @var array
     */
    protected $ipTablesRoutingData = [];

    /**
     * Constructor
     * -----------
     * Checks permissions, loads configuration
     *
     * @param WirelessInterface $interface
     * @throws ConfigurationException
     */
    public function __construct(WirelessInterface $interface)
    {
        parent::__construct($interface);

        $this->interface = $interface;
        $this->configPath = '/etc/hostapd/raspap/' . $interface->getName() . '.conf';

        // check permissions
        if (!is_writable(dirname($this->configPath)))
        {
            throw new ConfigurationException('Path "' . dirname($this->configPath) . '" is not writable for raspap user/group!', 'HOSTAPD_CONFIG_PATH_NOT_WRITABLE');
        }

        if (!is_file($this->configPath))
        {
            $fp = fopen($this->configPath, 'w');
            fwrite($fp, "interface=" . $interface->getName() . "\n");
            fclose($fp);
        }

        // load data into array to operate on, and then save back
        $this->ipTablesRoutingData = $this->interface->getDaemons()->get('iptablesRouting') ? $this->interface->getDaemons()->get('iptablesRouting') : [];
        $this->hostapdData = $this->interface->getDaemons()->get('hostapd') ? $this->interface->getDaemons()->get('hostapd') : [];

        $this->data = parse_ini_file($this->configPath);
    }

    /**
     * Returns configuration path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->configPath;
    }

    /**
     * Set MAC Address ACL
     *
     * @return $this
     */
    public function setWhiteListOnly()
    {
        $this->data['macaddr_acl'] = 1;
        $this->data['accept_mac_file'] = dirname($this->configPath) . '/' . $this->interface->getName() . '.accept';

        if (!is_file($this->data['accept_mac_file']))
        {
            $fp = fopen($this->data['accept_mac_file'], 'w');
            fwrite($fp, '');
            fclose($fp);
        }
    }

    /**
     * Disable MAC Address Access Control Lists
     *
     * @return $this
     */
    public function disableWhiteList()
    {
        $this->data['macaddr_acl'] = 0;
        return $this;
    }

    /**
     * Basic setup
     *
     * @param string $ssid
     * @param string $mode
     * @param int|string $channel
     * @param bool $hidden
     *
     * @return $this
     */
    public function setup($ssid, $mode = 'g', $channel = 'random', $hidden = false)
    {
        $this->clearKeys([
            'ieee80211n',
            'ieee80211ac',
            'hw_mode',
        ]);

        $mode = strtolower($mode);
        $this->data['ieee80211n'] = 0;
        $this->data['ssid'] = $ssid;

        // mode
        if (!in_array($mode, [ 'a', 'b', 'g', 'g/n' ]))
        {
            $mode = 'g';
        }

        if ($mode === 'g/n')
        {
            $this->data['ieee80211n'] = 1;
            $mode = 'g';
        }

        elseif ($mode === 'a')
        {
            $this->data['ieee80211n'] = 1;
            $this->data['ieee80211ac'] = 1;
            $mode = 'a';
        }

        // channel
        if ($channel === 'random')
        {
            $channel = rand(1, 13);
        }

        $this->setChannel($channel);

        // hidden network option
        $this->setNetworkVisibility(!intval($hidden));

        $this->data['hw_mode'] = $mode;
        return $this;
    }

    /**
     * Set encryption on a wireless network
     *
     * @param string $type
     * @param null $passphrase
     * @param string $pairwise
     */
    public function setEncryption($type = 'WPA2PerUserKey', $passphrase = null, $pairwise = 'CCMP TKIP')
    {
        $this->clearKeys([
            'auth_algs',
            'wpa',
            'wpa_psk_file',
            'wpa_psk',
            'rsn_pairwise',
            'wpa_pairwise',
            'wep_default_key',
            'wep_key0',
        ]);

        if ($type !== 'Open')
        {
            if (!in_array($pairwise, [ 'CCMP', 'TKIP', 'CCMP TKIP' ]))
            {
                $pairwise = 'TKIP';
            }

            if ($type === 'WPA2SharedKey' || $type === 'WEP' || $type === 'WPA')
            {
                if (strlen($passphrase) < 8)
                {
                    throw new \InvalidArgumentException('Passphrase must be at least 8 characters long');
                }
            }

            switch ($type)
            {
                case 'WPA2PerUserKey':
                case 'WPA2SharedKey':
                {
                    $this->data['wpa'] = 2;
                    $this->data['auth_algs'] = 1;

                    if ($type === 'WPA2SharedKey')
                    {
                        if (isset($this->data['wpa_passphrase']))
                        {
                            unset($this->data['wpa_passphrase']);
                        }

                        $this->data['wpa_psk'] = $this->generatePassphrase($passphrase);
                        $this->hostapdData['password'] = $passphrase;
                    }
                    else
                    {
                        $this->data['wpa_psk_file'] = dirname($this->configPath) . '/' . $this->interface->getName() . '.psk';

                        if (!is_file($this->data['wpa_psk_file']))
                        {
                            $fp = fopen($this->data['wpa_psk_file'], 'w');
                            fwrite($fp, '');
                            fclose($fp);
                        }
                    }

                    break;
                }

                case 'WPA':
                {
                    $this->data['wpa_psk'] = $this->generatePassphrase($passphrase);
                    $this->data['wpa'] = 1;
                    $this->data['auth_algs'] = 1;
                    break;
                }

                case 'WEP':
                {
                    if (isset($this->data['wpa']))
                    {
                        unset($this->data['wpa']);
                    }

                    $this->data['auth_algs'] = 2;
                    $this->data['wep_default_key'] = '0';
                    $this->data['wep_key0'] = bin2hex($passphrase);
                }
            }

            $this->data['wpa_pairwise'] = $pairwise;
            $this->data['rsn_pairwise'] = 'CCMP';
        }
    }

    /**
     * @param string $password
     */
    protected function generatePassphrase($password)
    {
        if (!isset($this->data['ssid']))
        {
            throw new \UnexpectedValueException('ssid not defined, please use setup() first');
        }

        $output = shell_exec('wpa_passphrase "' . $this->data['ssid'] . '" "' . $password . '"');
        preg_match('/psk\=([a-zA-Z0-9]+)/', $output, $matches);

        if (!is_array($matches) || !isset($matches[1]))
        {
            throw new \UnexpectedValueException('wpa_passphrase command returned invalid output - ' . $output);
        }

        return $matches[1];
    }

    /**
     * List of interfaces to bridge with
     *
     * @param string[] $interfaces
     * @return $this
     */
    public function bridgeWithInterfaces(array $interfaces)
    {
        if (!$interfaces)
        {
            $this->ipTablesRoutingData['bridge'] = null;
            $this->clearKeys(['bridge']);
            return $this;
        }

        $list = new InterfacesList();

        foreach ($interfaces as $key => $interface)
        {
            if (!$list->hasInterface($interface))
            {
                unset($interfaces[$key]);
            }
        }

        // generates a bridge interface name to setup bridge later
        $bridgeNumber = 0;
        while (file_exists('/sys/class/net/br' . $bridgeNumber))
        {
            $bridgeNumber++;
        }

        $this->ipTablesRoutingData['bridge'] = $interfaces;
        $this->set('bridge', 'br' . $bridgeNumber);

        return $this;
    }

    /**
     * Checks if current interface is in a bridge with other
     *
     * @param string $interfaceName
     * @return bool
     */
    public function isInBridgeWith($interfaceName)
    {
        if ($interfaceName instanceof AbstractInterface)
        {
            $interfaceName = $interfaceName->getName();
        }

        return isset($this->ipTablesRoutingData['bridge']) && in_array($interfaceName, $this->ipTablesRoutingData['bridge']);
    }

    /**
     * @return bool
     */
    public function isCreatingABridge()
    {
        return isset($this->ipTablesRoutingData['bridge']) && $this->ipTablesRoutingData['bridge'];
    }

    /**
     * Get plaintext password (not WPA-PSK)
     * Plaintext password is kept in SQLite3 database in daemons table
     * and PSK version is kept in configuration file
     *
     * @return string
     */
    public function getPassword()
    {
        return isset($this->hostapdData['password']) ? $this->hostapdData['password'] : '';
    }

    /**
     * @param BridgeInterface $bridgeInterface
     *
     * @return $this;
     */
    /*public function connectToBridgeInterface(BridgeInterface $bridgeInterface)
    {
        $this->data['bridge'] = $bridgeInterface->getName();
        return $this;
    }*/

    /**
     * DFS/Radar detection (ieee80211h)
     *
     * @param bool $value
     * @return $this
     */
    public function setDFS($value = true)
    {
        if (!$this->get('ieee80211d'))
        {
            $value = false;
        }

        $value = (bool)$value;
        $this->set('ieee80211h', (int)$value);
        return $this;
    }

    /**
     * DFS/Radar detection (ieee80211h)
     *
     * @return bool
     */
    public function getDFSValue()
    {
        return $this->get('ieee80211h', 0);
    }

    /**
     * Country code requirements (country_code)
     *
     * @param string $code
     * @return $this
     */
    public function setCountryCode($code = null)
    {
        if (!$code)
        {
            $this->clearKeys([
                'country_code',
                'ieee80211h',
                'ieee80211d',
            ]);

            return $this;
        }

        $this->set('country_code', $code);
        $this->set('ieee80211d', 1);
        return $this;
    }

    /**
     * Country code requirements (country_code)
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->get('country_code', '');
    }

    /**
     * Management Frame Protection (ieee80211w)
     *
     * @param bool $value
     * @return $this
     */
    public function setFrameProtection($value = false)
    {
        $value = (bool)$value;
        $this->set('ieee80211w', (int)$value);
        return $this;
    }

    /**
     * MFP (ieee80211w)
     *
     * @return bool
     */
    public function getFrameProtection()
    {
        return (bool)$this->get('ieee80211w', 0);
    }

    /**
     * Get network name (ssid)
     *
     * @return mixed|null
     */
    public function getName()
    {
        return $this->get('ssid');
    }

    /**
     * Hardware mode (hw_mode)
     *
     * @return mixed|null
     */
    public function getMode()
    {
        if ($this->get('ieee80211n'))
        {
            return 'g/n';
        }

        return $this->get('hw_mode', 'g');
    }

    /**
     * List of supported modes (hw_mode values)
     *
     * @todo Implement a real check basing on iwconfig/iw output
     * @return array
     */
    public function getSupportedModes()
    {
        return [
            'a', 'b', 'g', 'g/n',
        ];
    }

    /**
     * Channel (channel)
     *
     * @return int
     */
    public function getChannel()
    {
        return (int)$this->get('channel', 0);
    }

    /**
     * Hidden network (ignore_broadcast_ssid)
     *
     * @return bool
     */
    public function isHidden()
    {
        return (bool)intval($this->get('ignore_broadcast_ssid'));
    }

    /**
     * Set network to be hidden/showed (ignore_broadcast_ssid)
     *
     * @param bool $visibility
     * @return $this
     */
    public function setNetworkVisibility($visibility = true)
    {
        $this->data['ignore_broadcast_ssid'] = intval(!$visibility);
        return $this;
    }

    /**
     * Set channel (channel)
     *
     * @param int $channel
     * @return $this
     */
    public function setChannel($channel = 0)
    {
        if ($channel > 14 || $channel < 0)
        {
            throw new \InvalidArgumentException('$channel must be in range of <0,14>');
        }

        $this->data['channel'] = (int)$channel;
        return $this;
    }

    /**
     * Detect encryption type (wpa_psk_file, wpa_passphrase, wpa, auth_algs)
     *
     * @return string
     */
    public function getEncryptionType()
    {
        if ($this->get('wpa_psk_file') || $this->get('wpa_psk'))
        {
            if ($this->get('wpa_psk_file'))
            {
                return 'WPA2PerUserKey';
            }

            return 'WPA2SharedKey';
        }
        elseif ((string)$this->get('wpa') === '1')
        {
            return 'WPA';
        }
        elseif ((string)$this->get('auth_algs') === '2')
        {
            return 'WEP';
        }

        return 'Open';
    }

    /**
     * WPA Key Cipher (wpa_pairwise)
     *
     * @return mixed|null
     */
    public function getKeyCipher()
    {
        return strtoupper($this->get('wpa_pairwise'));
    }

    /**
     * Quality of Service (wmm_enabled)
     *
     * @return bool
     */
    public function usingQoS()
    {
        return (bool)intval($this->get('wmm_enabled', 0));
    }

    /**
     * Quality of Service (wmm_enabled)
     *
     * @param bool $enabled
     * @return $this
     */
    public function setQoS($enabled = false)
    {
        $this->data['wmm_enabled'] = intval($enabled);
        return $this;
    }

    /**
     * Get encryption password (wpa_passphrase)
     *
     * @return mixed|null
     */
    public function getPassphrase()
    {
        return $this->get('wpa_psk', '') ? $this->get('wpa_psk', '') : hex2bin($this->get('wep_key0', ''));
    }

    /**
     * Save all changes to configuration file, to database
     *
     * @param bool $backup
     *
     * @return bool|int
     */
    public function save($backup = true)
    {
        // reload dhcp cofiguration
        $dhcp = new DHCPD($this->interface);
        $dhcp->save();

        $this->interface->getDaemons()->clear();

        if ($this->usingQoS())
        {
            $this->interface->getDaemons()->put('qos');
        }

        $this->interface->getDaemons()->put('dhcpd');
        $this->interface->getDaemons()->put('iptablesRouting', $this->ipTablesRoutingData);
        $this->interface->getDaemons()->put('hostapd', $this->hostapdData);
        $this->interface->setRole('access_point');
        $this->interface->save();

        if ($backup)
        {
            if (is_file($this->configPath))
            {
                copy($this->configPath, dirname($this->configPath) . '/.' . $this->getName() . '.backup.previous.conf');
            }
        }

        $result = IniSupport::write_ini_file($this->data, $this->configPath, false, '');
        chmod($this->configPath, 774);
        return $result;
    }
}