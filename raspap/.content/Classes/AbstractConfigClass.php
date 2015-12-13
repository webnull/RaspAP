<?php
namespace RaspAP\Classes;

use Panthera\Components\Kernel\BaseFrameworkClass;
use RaspAP\Components\NetworkInterface\AbstractInterface;
use RaspAP\Components\NetworkInterface\WirelessInterface;

class AbstractConfigClass extends BaseFrameworkClass
{
    /**
     * @see get() method for getter
     * @var array $data
     */
    protected $data = [];

    /**
     * @var WirelessInterface $interface
     */
    protected $interface;

    /**
     * Constructor
     *
     * @param AbstractInterface $interface
     */
    public function __construct(AbstractInterface $interface)
    {
        if (!$interface instanceof AbstractInterface)
        {
            throw new \InvalidArgumentException('$interface must be an instance of AbstractInterface');
        }

        $this->interface = $interface;
    }

    /**
     * Getter method for @see $data
     *
     * @param string $parameter
     * @param null|mixed $default
     *
     * @return mixed|null
     */
    protected function get($parameter, $default = null)
    {
        return isset($this->data[$parameter]) ? $this->data[$parameter] : $default;
    }

    /**
     * Unset list of keys
     *
     * @param array $keys
     *
     * @return $this
     */
    protected function clearKeys($keys = null)
    {
        if ($keys === null)
        {
            $this->data = [];
            return $this;
        }

        foreach ($keys as $key)
        {
            if (isset($this->data[$key]))
            {
                unset($this->data[$key]);
            }
        }

        return $this;
    }

    /**
     * Set parameter
     *
     * @see $data
     * @param string $paramter
     * @param mixed $value
     *
     * @return $this
     */
    protected function set($paramter, $value)
    {
        $this->data[$paramter] = $value;
        return $this;
    }
}