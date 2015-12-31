<?php
namespace RaspAP\Components\TOR;
use Panthera\Classes\BaseExceptions\FileException;
use Panthera\Classes\BaseExceptions\ValidationException;
use Panthera\Components\Validator\Validator;

/**
 * RaspAP
 * --
 * Class Privoxy
 *
 * @package RaspAP\Components\TOR
 */
class Privoxy
{
    /** @var array $data */
    protected $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!is_file('/etc/privoxy/config-raspap') || !is_writable('/etc/privoxy/config-raspap'))
        {
            throw new FileException('/etc/privoxy/config-raspap is not writable for RaspAP web panel user', 'PRIVOXY_CONFIG_NOT_WRITABLE');
        }

        $this->parse();
    }

    /**
     * Parse Privoxy configuration file into $this->data array of keys and values
     */
    protected function parse()
    {
        $contents = explode("\n", file_get_contents('/etc/privoxy/config-raspap'));

        foreach ($contents as $line)
        {
            preg_match('/([a-zA-Z0-9\-\_]+)\s*(.*+)/', $line, $matches);

            if (count($matches) === 3)
            {
                $this->data[$matches[1]] = $matches[2];
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $listenAddress
     * @param int $listenPort
     *
     * @throws ValidationException
     */
    public function setupTOR($listenAddress, $listenPort = 8118)
    {
        if (!Validator::validate($listenAddress, '!Classes/IP::address'))
            throw new ValidationException('Invalid IP Address specified', 'PRIVOXY_INVALID_IP');

        if (!Validator::validate($listenPort, '!integer'))
            throw new ValidationException('Invalid port number', 'PRIVOXY_INVALID_PORT');

        $this->set('listen-address', $listenAddress . ':' . $listenPort);
        $this->set('forward-socks5', '/ localhost:9050 .');
    }

    /**
     * Save configuration to file
     *
     * @return $this
     */
    public function save()
    {
        $content = "";

        foreach ($this->data as $key => $value)
        {
            $content .= $key . " " .$value . "\n";
        }

        $fp = fopen('/etc/privoxy/config-raspap', 'w');
        fwrite($fp, $content);
        fclose($fp);

        return $this;
    }
}