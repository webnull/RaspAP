<?php
namespace RaspAP\Components\NetworkInterface\Helpers;

/**
 * RaspAP
 * --
 * InterfaceDaemons
 *
 * @package RaspAP\Components\NetworkInterface\Helpers
 */
class InterfaceDaemons
{
    /** @var array $daemons */
    protected $daemons = [];

    /**
     * Constructor
     *
     * @param array $daemons
     */
    public function __construct($daemons)
    {
        if ($daemons)
        {
            $this->daemons = json_decode($daemons, true);
        }
    }

    /**
     * @param null $daemonName
     * @return array|null
     */
    public function get($daemonName = null)
    {
        if ($daemonName === null)
        {
            return $this->daemons;
        }

        return isset($this->daemons[$daemonName]) ? $this->daemons[$daemonName] : null;
    }

    /**
     * Add daemon to the list
     *
     * @param string $daemonName
     * @param array|string|null|int $data
     *
     * @return $this
     */
    public function put($daemonName, $data = null)
    {
        $this->daemons[$daemonName] = $data;
        return $this;
    }

    /**
     * Clear list of daemons
     *
     * @param string|null $daemonName
     * @return $this
     */
    public function clear($daemonName = null)
    {
        if ($daemonName && isset($this->daemons[$daemonName]))
        {
            unset($this->daemons[$daemonName]);
            return $this;
        }

        $this->daemons = [];
        return $this;
    }

    /**
     * @magic
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->daemons);
    }
}