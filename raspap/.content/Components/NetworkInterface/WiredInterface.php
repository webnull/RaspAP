<?php
namespace RaspAP\Components\NetworkInterface;

use Panthera\Classes\BaseExceptions\PantheraFrameworkException;

/**
 * RaspAP
 * --
 * Wired interface management
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 * @package RaspAP\Components\NetworkInterface
 */
class WiredInterface extends AbstractInterface
{
    /** @var string $name */
    protected $name = '';

    /** @var string $output */
    protected $output = '';

    /** @var null|bool $bridgeStatus */
    protected $bridgeStatus = null;

    /**
     * Parse ifconfig output
     *
     * @throws PantheraFrameworkException
     */
    public function parse()
    {
        // MAC Address
        $this->extractData('/(ether|HWaddr) ([0-9a-f:]+)/i', 'MAC');

        // Associated IPv4 address
        $this->extractData('/(inet |inet addr:)([0-9.]+)/i', 'IPv4');

        // IPv4 Broadcast
        $this->extractData('/(broadcast:|broadcast )([0-9.]+)/i', 'IPv4_Broadcast');

        // MTU
        $this->extractData('/(mtu:|mtu )([0-9]+)/i', 'MTU');

        // Netmask
        $this->extractData('/mask([: ])?([0-9.]+)/i', 'Netmask');

        // Associated IPv6 address
        $this->extractData('/(inet6 addr: |inet6 )([0-9a-z\: ]+)/i', 'IPv6');

        // RX packets
        $this->parsePackets('received');

        // TX packets
        $this->parsePackets('transmitted');

        // gateway address (from "route" output)
        $routeOutput = shell_exec('route |grep "' . $this->getName() . '"');
        $gateway = '';

        if ($routeOutput)
        {
            preg_match('/([0-9\.]+)\s*/', $routeOutput, $matches);

            if ($matches)
            {
                $gateway = $matches[1];
            }
        }

        $this->details['Gateway'] = $gateway;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return (isset($this->details['IPv4']) && $this->details['IPv4']) || $this->isBridgeConnected();
    }

    /**
     * Returns IPv4 Address
     *
     * @return string
     */
    public function getIPAddress()
    {
        return isset($this->details['IPv4']) ? $this->details['IPv4'] : '';
    }

    /**
     * @return string
     */
    public function getBroadcastAddress()
    {
        return isset($this->details['IPv4_Broadcast']) ? $this->details['IPv4_Broadcast'] : '';
    }

    /**
     * @return string
     */
    public function getNetmaskAddress()
    {
        return isset($this->details['Netmask']) ? $this->details['Netmask'] : '';
    }

    /**
     * @return string
     */
    public function getGatewayAddress()
    {
        return isset($this->details['Gateway']) ? $this->details['Gateway'] : '';
    }

    /**
     * Received packets
     *
     * Example:
     *  RX packets 22152  bytes 18409967
     *  RX errors 0  dropped 0  overruns 0  frame 0
     *
     * @param string $type
     * @throws PantheraFrameworkException
     */
    protected function parsePackets($type)
    {
        $success = false;

        // RX errors 0  dropped 0  overruns 0  frame 0
        if (
            preg_match('/' . (($type === 'received') ? 'RX' : 'TX') . ' packets(:|) ([0-9]+)([ ]+)?bytes([ ]+)([0-9]+)/i', $this->output, $data) &&
            preg_match('/' . (($type === 'received') ? 'RX' : 'TX') . ' errors([ ]+)?([0-9]+)([ ]+)?dropped([ ]+)([0-9]+)([ ]+)?overruns([ ]+)([0-9]+)/i', $this->output, $dataErrors)
        )
        {
            if (count($data) === 6 && count($dataErrors) === 9)
            {
                $this->details[$type] = [
                    'packets' => (int)$data[2],
                    'bytes'   => (int)$data[5],
                    'errors'  => (int)$dataErrors[2],
                    'dropped' => (int)$dataErrors[5],
                    'overruns'=> (int)$dataErrors[8],
                ];

                $success = true;
            }
        }
        else
        {
            preg_match('/' . (($type === 'received') ? 'RX' : 'TX') . ' packets:([0-9]+) errors:([0-9]+) dropped:([0-9]+) overruns:([0-9]+)/i', $this->output, $data);

            if (count($data) === 5)
            {
                $this->details[$type] = [
                    'packets' => $data[1],
                    'bytes'   => 0,
                    'errors'  => $data[2],
                    'dropped' => $data[3],
                    'overruns'=> $data[4],
                ];

                $success = true;
            }
        }


        if (!$success)
        {
            throw new PantheraFrameworkException('Regexp error, cannot parse ifconfig output for received/transmitted packets', 'RX_PARSING_ERROR');
        }
    }

    /**
     * @return array
     */
    public function getPossibleRoles()
    {
        if ($this->getName() === 'lo')
        {
            return [

            ];
        }

        return [
            'client_cable_dhcp',
            'client_cable_static',
            //'access_point',
            //'monitor',
            'down',
        ];
    }

    /**
     * Check if interface is used in a bridge
     *
     * @return bool
     */
    public function isBridgeConnected()
    {
        if ($this->bridgeStatus === null)
        {
            $output = shell_exec('brctl show');
            $lines = array_map(function ($line)
                {
                    return $line . ";\n";
                }, explode("\n", $output));
            $output = implode("\n", $lines);

            $this->bridgeStatus = strpos($output, $this->getName() . ';') !== false;
        }

        return $this->bridgeStatus;
    }

    /**
     * @return bool
     */
    public function canBeUsedInBridge()
    {
        return $this->getName() !== 'lo';
    }
}