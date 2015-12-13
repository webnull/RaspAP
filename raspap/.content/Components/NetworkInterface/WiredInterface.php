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
        $this->extractData('/(inet6 |inet6 addr:)([0-9a-z\:]+)/i', 'IPv6');

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
        return (isset($this->details['IPv4']) && $this->details['IPv4']);
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
        preg_match('/' . (($type === 'received') ? 'RX' : 'TX') . ' packets(:|) ([0-9]+)([ ]+)?bytes([ ]+)([0-9]+)/i', $this->output, $data);

        // RX errors 0  dropped 0  overruns 0  frame 0
        preg_match('/' . (($type === 'received') ? 'RX' : 'TX') . ' errors([ ]+)?([0-9]+)([ ]+)?dropped([ ]+)([0-9]+)([ ]+)?overruns([ ]+)([0-9]+)/i', $this->output, $dataErrors);

        if (count($data) === 6 && count($dataErrors) === 9)
        {
            $this->details[$type] = [
                'packets' => (int)$data[2],
                'bytes'   => (int)$data[5],
                'errors'  => (int)$dataErrors[2],
                'dropped' => (int)$dataErrors[5],
                'overruns'=> (int)$dataErrors[8],
            ];
        }
        else
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
}