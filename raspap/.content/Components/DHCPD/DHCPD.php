<?php
namespace RaspAP\Components\DHCPD;

use Panthera\Classes\BaseExceptions\PantheraFrameworkException;
use RaspAP\Classes\AbstractConfigClass;
use RaspAP\Components\NetworkInterface\WirelessInterface;

class DHCPD extends AbstractConfigClass
{
    protected $data = [
        'dns' => [
            '8.8.8.8',
            '8.8.4.4',
        ],
        'routerAddress' => '192.168.161.1',
        'range' => [
            '192.168.161.2',
            '192.168.161.254',
        ],
        'subnet' => '192.168.161.0',
        'broadcast' => '192.168.161.255',
        'netMask' => '255.255.255.0',
        'lease' => [
            'default' => 600,
            'max' => 7200,
        ],
        'domain' => 'local',
    ];

    /**
     * Constructor
     * Parses configuration file
     *
     * @param WirelessInterface $interface
     */
    public function __construct(WirelessInterface $interface)
    {
        parent::__construct($interface);

        if (is_file('/etc/dhcpd/raspap/' . $this->interface->getName() . '.conf'))
        {
            $this->parseConfigurationFile();
        }
    }

    /**
     * Parse '/etc/dhcpd/raspap/%interface%.conf' file
     * and put everything into $data
     */
    protected function parseConfigurationFile()
    {
        $contents = file_get_contents('/etc/dhcpd/raspap/' . $this->interface->getName() . '.conf');

        // subnet/netMask
        preg_match('/subnet ([0-9\.]+) netmask ([0-9\.]+) \{/i', $contents, $matches);
        $this->set('subnet', $matches[1]);
        $this->set('netMask', $matches[2]);

        // subnet range
        preg_match('/range ([0-9\.]+) ([0-9\.]+);/i', $contents, $matches);
        $this->set('range', [
            $matches[1],
            $matches[2],
        ]);

        // broadcast
        preg_match('/option broadcast\-address ([0-9\.]+);/i', $contents, $matches);
        $this->set('broadcast', $matches[1]);

        // routerAddress
        preg_match('/option routers ([0-9\.]+);/i', $contents, $matches);
        $this->set('routerAddress', $matches[1]);

        // routerAddress
        preg_match('/default\-lease\-time ([0-9]+)\;/i', $contents, $matches);
        preg_match('/max\-lease\-time ([0-9]+);/i', $contents, $matchesMax);
        $this->set('lease', [
            'default' => (int)$matches[1],
            'max' => (int)$matchesMax[1],
        ]);

        // domain
        preg_match('/option domain\-name \"(.*)\";/i', $contents, $matches);
        $this->set('domain', $matches[1]);

        // dns
        preg_match('/option domain\-name\-servers ([0-9\.]+), ([0-9\.]+)/i', $contents, $matches);
        $this->set('dns', [
            $matches[1],
            $matches[2],
        ]);
    }

    /**
     * ifconfig wlp0s20u1 192.168.42.1
     *
     * @param string $address
     * @return $this
     */
    public function setRouterAddress($address = '192.168.161.1')
    {
        $this->set('routerAddress', $address);
        return $this;
    }

    /**
     * Setup a network
     *
     * @param string $routerAddress Cannot be same as subnet address, and cannot be in range of $rangeStarts - $rangeEnds
     * @param string $subnet Subnet address eg. 192.168.161.0
     * @param string $rangeStarts Starting range, eg. 192.168.161.100
     * @param string $rangeEnds Ending range eg. 192.168.161.250
     * @param string $mask Network mask eg. 255.255.255.0
     *
     * @return $this
     */
    public function setupSubnet($routerAddress = '192.168.161.1', $subnet = '192.168.161.0', $rangeStarts = '192.168.161.2', $rangeEnds = '192.168.161.254', $mask = '255.255.255.0')
    {
        $this->set('routerAddress', $routerAddress);
        $this->set('range', [
            $rangeStarts,
            $rangeEnds,
        ]);
        $this->set('subnet', $subnet);
        $this->set('netMask', $mask);

        return $this;
    }


    /**
     * DHCP Lease time
     *
     * @param int $default
     * @param int $max
     *
     * @return $this
     */
    public function setLeaseTime($default = 600, $max = 7200)
    {
        $this->set('lease', [
            'default' => (int)$default,
            'max' => (int)$max,
        ]);

        return $this;
    }

    /**
     * @param string $domainName
     * @return $this
     */
    public function setDomain($domainName = 'local')
    {
        $this->set('domain', $domainName);
        return $this;
    }

    /**
     * Generate configuration and save to file
     * '/etc/dhcpd/raspap/%interface%.conf'
     *
     * To run a dhcp server type:
     *   dhcpd -cf '/etc/dhcpd/raspap/%interface%.conf' %interface%
     *
     * @throws PantheraFrameworkException
     * @return string Path to configuration file
     */
    public function save()
    {
        $content = "\nauthoritative;\nsubnet " . $this->get("subnet") . " netmask " . $this->get("netMask") . " {
        range " . $this->get("range")[0] . " " . $this->get("range")[1] . ";
        option broadcast-address " . $this->get("broadcast") . ";
        option routers " . $this->get("routerAddress") . ";
        default-lease-time " . $this->get("lease")["default"] . ";
        max-lease-time " . $this->get("lease")["max"] . ";
        option domain-name \"" . $this->get("domain") . "\";
        option domain-name-servers " . $this->get("dns")[0] . ", " . $this->get("dns")[1] . ";
        }";

        if (!is_dir('/etc/dhcpd/') || !is_dir('/etc/dhcpd/raspap'))
        {
            throw new PantheraFrameworkException('/etc/dhcpd/raspap does not exists', 'DHCP_NO_DIRECTORY');
        }

        if (!is_writable('/etc/dhcpd/raspap/'))
        {
            throw new PantheraFrameworkException('/etc/dhcpd/raspap is not writable from current context', 'DHCP_DIRECTORY_NOT_WRITABLE');
        }

        $fp = fopen('/etc/dhcpd/raspap/' . $this->interface->getName() . '.conf', 'w');
        fwrite($fp, $content);
        fclose($fp);

        return '/etc/dhcpd/raspap/' . $this->interface->getName() . '.conf';
    }
}