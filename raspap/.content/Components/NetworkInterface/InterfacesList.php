<?php
namespace RaspAP\Components\NetworkInterface;

/**
 * RaspAP
 * --
 * InterfacesList class, a collection of network interfaces
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 * @package RaspAP\Components\NetworkInterface
 */
class InterfacesList extends \Panthera\Components\Kernel\BaseFrameworkClass
{
    protected $wirelessInterfaces = [];
    protected $wiredInterfaces = [];

    /**
     * @var WiredInterface[]|WirelessInterface[]
     */
    protected $interfaces = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parseIWConfig();
        $this->parseIFConfig();

        if ($this->wirelessInterfaces)
        {
            foreach ($this->wirelessInterfaces as $interface => $output)
            {
                $this->interfaces[$interface] = new WirelessInterface($interface, $output . "\n\n" . $this->wiredInterfaces[$interface]);
            }
        }

        if ($this->wiredInterfaces)
        {
            foreach ($this->wiredInterfaces as $interface => $output)
            {
                if (isset($this->interfaces[$interface]))
                {
                    continue;
                }

                if (substr($interface, 0, 2) == 'br')
                {
                    $this->interfaces[$interface] = new BridgeInterface($interface, $output);
                }
                else
                {
                    $this->interfaces[$interface] = new WiredInterface($interface, $output);
                }
            }
        }
    }

    /**
     * @return WiredInterface[]|WirelessInterface[]
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Check if interface exists
     *
     * @param string $name
     * @return bool
     */
    public function hasInterface($name)
    {
        return isset($this->interfaces[$name]);
    }

    /**
     * Parse basically only names of interfaces from "iwconfig" output
     */
    protected function parseIWConfig()
    {
        $output = shell_exec('iwconfig');
        preg_match_all('/([a-z0-9]+)(.*)IEE/i', $output, $interfaces);

        if (!count($interfaces) || !count($interfaces[1]))
        {
            return false;
        }

        $lines = explode("\n", $output);

        foreach ($interfaces[1] as $interface)
        {
            $this->wirelessInterfaces[$interface] = '';
            $started = false;

            foreach ($lines as $line)
            {
                if (strpos($line, $interface) === 0)
                {
                    $started = true;
                }
                elseif (!trim($line))
                {
                    $started = false;
                }

                if ($started)
                {
                    $this->wirelessInterfaces[$interface] .= $line . "\n";
                }
            }
        }
    }

    /**
     * Parse output of "ifconfig" to get list of all cable interfaces
     */
    protected function parseIFConfig()
    {
        $output = shell_exec('ifconfig -a');
        preg_match_all('/([a-z0-9]+)\: flags/i', $output, $interfaces);

        $lines = explode("\n", $output);

        if (!count($interfaces) || !count($interfaces[1]))
        {
            return false;
        }

        foreach ($interfaces[1] as $interface)
        {
            //if (isset($this->wirelessInterfaces[$interface]))
            //{
            //    continue;
            //}

            $started = false;
            $this->wiredInterfaces[$interface] = '';

            foreach ($lines as $line)
            {
                if (strpos($line, $interface) === 0)
                {
                    $started = true;
                }
                elseif (!trim($line))
                {
                    $started = false;
                }

                if ($started)
                {
                    $this->wiredInterfaces[$interface] .= $line . "\n";
                }
            }
        }
    }
}