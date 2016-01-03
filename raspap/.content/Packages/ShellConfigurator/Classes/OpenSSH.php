<?php
namespace RaspAP\Packages\ShellConfigurator\Classes;
use Panthera\Classes\BaseExceptions\InvalidArgumentException;

/**
 * RaspAP
 * --
 * Class OpenSSH
 *
 * @package RaspAP\Packages\ShellConfigurator\Classes
 */
class OpenSSH
{
    /** @var string[] $unparsed */
    protected $unparsed = [];

    /** @var array $variables */
    protected $variables = [];

    /** @var array $knownVars */
    protected $knownVars = [
        'Port'               => 'Port\s*([0-9]+)',
        'MaxSessions'        => 'MaxSessions\s*([0-9]+)',
        'ListenAddress'      => 'ListenAddress\s*([0-9\.]+)',
        'Protocol'           => 'Protocol\s*(1|2)',
        'PermitTunnel'       => 'PermitTunnel\s*(yes|no)',
        'X11Forwarding'      => 'X11Forwarding\s*(yes|no)',
        'AllowTcpForwarding' => 'AllowTcpForwarding\s*(yes|no)',
        'PrintLastLog'       => 'PrintLastLog\s*(yes|no)',
        'PermitRootLogin'    => 'PermitRootLogin\s*(yes|no|prohibit-password)',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        // if it's an empty template file, then copy contents from distribution's /etc/ssh/sshd_config
        if (!filesize('/etc/ssh/sshd_raspap'))
        {
            $fp = fopen('/etc/ssh/sshd_raspap', 'w');
            fwrite($fp, file_get_contents('/etc/ssh/sshd_config'));
            fclose($fp);
        }

        // we will be writing to /etc/ssh/sshd_raspap, and raspapd daemon will be copying it into /etc/ssh/sshd_config as root
        $this->unparsed = explode("\n", file_get_contents('/etc/ssh/sshd_raspap'));
        $this->parse();
    }

    /**
     * @param string $regexp
     * @return string
     */
    protected function getRegexp($regexp)
    {
        return '/(#?)' . $regexp . '/i';
    }

    /**
     * Parse file
     */
    public function parse()
    {
        foreach ($this->unparsed as $lineNum => $line)
        {
            foreach ($this->knownVars as $varName => $regexp)
            {
                preg_match($this->getRegexp($regexp), $line, $match);

                if ($match)
                {
                    $entry = [
                        'name'   => $varName,
                        'active' => $match[1] !== '#',
                        'line'   => $lineNum,
                        'value'  => $match[2],
                    ];

                    $this->variables[$varName] = $entry;
                }
            }
        }
    }

    /**
     * @param string $var
     * @param string $value
     * @param null|bool $active
     *
     * @throws InvalidArgumentException
     * @return $this
     */
    public function set($var, $value, $active = null)
    {
        // validation first
        if (!isset($this->knownVars[$var]))
        {
            throw new InvalidArgumentException('"' . $var . '" variable is not supported', 'UNSUPPORTED_SSH_VAR');
        }

        $testString = $var . ' ' . $value;
        $test = preg_match($this->getRegexp($this->knownVars[$var]), $testString, $match);

        if (!$test)
        {
            throw new InvalidArgumentException('Invalid value for "' . $var . '"', 'INVALID_SSH_VAR_VALUE');
        }


        // adding new variable or editing an existing one
        if (!isset($this->variables[$var]))
        {
            $this->variables[$var] = [
                'name'   => $var,
                'active' => $active ? true : false,
                'line'   => -1,
                'value'  => $value,
            ];
        }
        else
        {
            $this->variables[$var]['value'] = $value;

            if ($active !== null && is_bool($active))
            {
                $this->variables[$var]['active'] = $active;
            }
        }

        $line = $this->variables[$var]['line'];
        $prefix = $this->variables[$var]['active'] ? '' : '#';

        if ($line === -1)
        {
            $this->unparsed[] = $prefix . $var . ' ' . $value;
        }
        else
        {
            $this->unparsed[$line] = $prefix . $var . ' ' . $value;
        }

        return $this;
    }

    /**
     * @param string $var
     * @return null|string
     */
    public function get($var)
    {
        if ($var === null)
        {
            return $this->variables;
        }

        return isset($this->variables[$var]) ? $this->variables[$var]['value'] : null;
    }

    /**
     * Save back to file
     */
    public function save()
    {
        $fp = fopen('/etc/ssh/sshd_raspap', 'w');
        fwrite($fp, implode("\n", $this->unparsed));
        fclose($fp);
    }
}