<?php
namespace RaspAP\Components\DHCPD;

use Panthera\Classes\BaseExceptions\FileException;
use RaspAP\Components\MacAddress\MacAddress;

/**
 * RaspAP
 * --
 * dhcpd.leases parser
 *
 * @package RaspAP\Components\DHCPD
 */
class Leases
{
    /** @var string $path Path to ISC-DHCPD leases file */
    protected $path = '/var/lib/dhcp/dhcpd.leases';

    /** @var array $leases */
    protected $leases = [];

    /**
     * @throws FileException
     */
    public function __construct()
    {
        $this->parse();
    }

    /**
     * Parses dhcpd.leases file, and puts results into $this->leases
     *
     * @throws FileException
     */
    protected function parse()
    {
        if (!is_file($this->path))
        {
            throw new FileException('File "' . $this->path . '" not found, please make sure you have isc-dhcpd server running', 'DHCPD_NO_LEASES_FILE');
        }

        $contents = explode("\n", file_get_contents($this->path));
        $leases = [];
        $currentLease = '';

        foreach ($contents as $line)
        {
            $line = rtrim(trim($line), ';');

            if (strpos($line, 'lease') === 0)
            {
                preg_match('/lease ([0-9\.]+)/i', $line, $matches);
                $leases[$matches[1]] = [];
                $currentLease = $matches[1];
            }

            elseif (strpos($line, '}') === 0)
            {
                $currentLease = '';
            }

            elseif ($currentLease)
            {
                $parts = explode(' ', $line);
                $attributeName = $parts[0];
                unset($parts[0]);

                if ($parts[1] === '4' || $parts[1] === '6')
                {
                    $attributeName .= '_ipv' . $parts[1];
                    unset($parts[1]);
                }

                if (in_array($attributeName, ['starts_ipv4', 'ends_ipv4', 'tstp_ipv4', 'cltt_ipv4']))
                {
                    $parts = implode(' ', $parts);
                }

                if ($attributeName === 'hardware')
                {
                    /** @var MacAddress $mac */
                    $mac = MacAddress::fetchOne([
                        '|=|mac' => $parts[2],
                    ]);

                    if ($mac)
                    {
                        $leases[$currentLease]['name'] = $mac->getTitle();
                    }
                }

                $leases[$currentLease][$attributeName] = $parts;
            }
        }

        $this->leases = $leases;
    }

    /**
     * @return array
     */
    public function getLeases()
    {
        return $this->leases;
    }
}