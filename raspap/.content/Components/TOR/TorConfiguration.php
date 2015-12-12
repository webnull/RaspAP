<?php
namespace RaspAP\Components\TOR;

use Panthera\Classes\BaseExceptions\FileException;
use Panthera\Components\Kernel\BaseFrameworkClass;

/**
 * RaspAP
 * --
 * Class TorConfiguration
 *
 * @package RaspAP\Components\TOR
 */
class TorConfiguration extends BaseFrameworkClass
{
    /** @var string $config */
    protected $config = '';

    /**
     * Constructor
     *
     * @throws FileException
     */
    public function __construct()
    {
        parent::__construct();

        if (!is_file('/etc/tor/torrc-raspap') || !is_writable('/etc/tor/torrc-raspap'))
        {
            throw new FileException('"/etc/tor/torrc-raspap" is not writable or does not exists', 'TOR_CONFIG_NOT_WRITABLE');
        }

        $this->config = file_get_contents('/etc/tor/torrc-raspap');
    }

    /**
     * Configure TOR as an internal node (Relay)
     *
     * @param string $address Public IP Address
     * @param string $nickname Nickname in TOR network
     * @param int $rate Maximum rate of default speed
     * @param int $burst Burst rate
     * @param array $exitPolicy List of accepted ports if configured as exit node
     *
     * @return null
     */
    public function configureRelay($address, $nickname, $rate = 100, $burst = 150, $exitPolicy = [])
    {
        $template = "RunAsDaemon 1
                    ORPort 9001
                    Address " . $address . "
                    OutboundBindAddress " . $address . "
                    Nickname " . $nickname. "

                    RelayBandwidthRate " . $rate . " KB
                    RelayBandwidthBurst " . $burst . " KB\n";

        if (!$exitPolicy)
        {
            $template .= "\n ExitPolicy reject *:*";
        }
        else
        {
            $template .= "\n ExitPolicy reject *:*\n";

            foreach ($exitPolicy as $port)
            {
                $template .= "\n ExitPolicy accept *:" . (int)$port . "\n ";
            }
        }

        return $this->save($template);
    }

    /**
     * Configure TOR in bridge mode
     *
     * @return null
     */
    public function configureBridge()
    {
        $template = "RunAsDaemon 1
                    ## default bridge port 443
                    ORPort 443

                    ## uncomment if you don't want torproject.org to know your bridge
                    # PublishServerDescriptor 0

                    SocksPort 9050
                    SocksListenAddress 127.0.0.1
                    BridgeRelay 1
                    ExitPolicy reject *:* ";

        return $this->save($template);
    }

    /**
     * @param string $template
     * @return null
     */
    protected function save($template)
    {
        $fp = fopen('/etc/tor/torrc-raspap', 'w');
        fwrite($fp, $template);
        fclose($fp);

        $this->config = $template;
    }

    /**
     * Get IPv4 bind address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->regexpGetMatch('/Address ([0-9\.]+)/i');
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->regexpGetMatch('/Nickname ([A-Za-z0-9\.]+)/i');
    }

    /**
     * RelayBandwidthRate
     *
     * @return int
     */
    public function getRelayBandwidthRate()
    {
        return (int)$this->regexpGetMatch('/RelayBandwidthRate ([0-9]+)/i');
    }

    /**
     * RelayBandwidthBurst
     *
     * @return int
     */
    public function getRelayBandwidthBurst()
    {
        return (int)$this->regexpGetMatch('/RelayBandwidthBurst ([0-9]+)/i');
    }

    /**
     * Get list of all allowed ports (ExitPolicy accept)
     *
     * @return int[]
     */
    public function getAllowedPorts()
    {
        // ExitPolicy accept *:22
        preg_match_all('/ExitPolicy\s*accept\s*\*\:([0-9]+)/i', $this->config, $matches);

        return $matches[1];
    }

    /**
     * @param string $regex
     * @return mixed
     */
    protected function regexpGetMatch($regex)
    {
        preg_match($regex, $this->config, $results);

        if (!isset($results[1]) || !$results[1])
        {
            return '';
        }

        return $results[1];
    }
}