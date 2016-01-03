<?php
namespace RaspAP\Packages\ShellConfigurator\Classes;

use Panthera\Components\Validator\Validator;
use RaspAP\Components\NetworkInterface\AbstractInterface;
use Panthera\Components\Kernel\Framework;
use Panthera\Components\Orm\ORMBaseFrameworkObject;
use RaspAP\Components\NetworkInterface\Helpers\InterfaceDaemons;

/**
 * RaspAP
 * --
 * SSHEntity represents SSH configuration in "interfaces" table
 * Configuration is stored in an entry as "ssh0" dummy interface
 *
 * @package RaspAP\Components\TOR
 */
class SSHEntity extends AbstractInterface
{
    /** @var OpenSSH $openSSH */
    protected $openSSH;

    /** @var array $sshData */
    protected $sshData;

    /**
     * Constructor
     */
    public function __construct()
    {
        $data = Framework::getInstance()->database->query('SELECT * FROM interfaces WHERE name = :name', [
            'name' => 'ssh0',
        ]);

        if ($data && isset($data[0]))
        {
            ORMBaseFrameworkObject::__construct($data[0]);
        }
        else
        {
            ORMBaseFrameworkObject::__construct(null);
        }

        $this->interfaceName = 'ssh0';
        $this->daemonsList = new InterfaceDaemons($this->interfaceDaemons);
        $this->openSSH = new OpenSSH();
        $this->sshData = $this->getDaemons()->get('SecureShellServer') ? $this->getDaemons()->get('SecureShellServer') : [];
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function parse()
    {
        return false;
    }

    /**
     * Set up OpenSSH
     *
     * @param string $listen
     * @param int $port
     * @param int $protocol
     * @param int $x11Forwarding
     * @param int $tcpForwarding
     * @param int $lastLogPrint
     * @param int $permitTunneling
     * @param int $rootLogin
     * @param int $maxSessions
     *
     * @throws \Panthera\Classes\BaseExceptions\InvalidArgumentException
     * @return $this
     */
    public function setupOpenSSH($listen = '0.0.0.0', $port = 22, $protocol = 2, $x11Forwarding = 1, $tcpForwarding = 1, $lastLogPrint = 1, $permitTunneling = 1, $rootLogin = 0, $maxSessions = 10)
    {
        $this->sshData['openssh'] = true;
        $this->openSSH->set('ListenAddress', $listen, true);
        $this->openSSH->set('Port', (int)$port, true);
        $this->openSSH->set('Protocol', $protocol, true);
        $this->openSSH->set('X11Forwarding', (int)$x11Forwarding ? 'yes' : 'no', true);
        $this->openSSH->set('AllowTcpForwarding', (int)$tcpForwarding ? 'yes' : 'no', true);
        $this->openSSH->set('PrintLastLog', (int)$lastLogPrint ? 'yes' : 'no', true);
        $this->openSSH->set('PermitTunnel', (int)$permitTunneling ? 'yes' : 'no', true);
        $this->openSSH->set('PermitRootLogin', (int)$rootLogin ? 'yes' : 'no', true);
        $this->openSSH->set('MaxSessions', (int)$maxSessions, true);

        return $this;
    }

    /**
     * Setup shellinaboxd (a web server that provides access to shell in a browser)
     *
     * @param int $port
     * @param bool $restrict
     *
     * @throws \Panthera\Classes\BaseExceptions\ValidationException
     * @return $this
     */
    public function setupShellInABox($port = 8021, $restrict = true)
    {
        $this->sshData['shellinabox'] = true;
        $this->sshData['shellinabox_port'] = Validator::validate($port, '!integer') ? $port : 8021;
        $this->sshData['shellinabox_restrict'] = (bool)$restrict;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableShellInABox()
    {
        $this->sshData['shellinabox'] = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableOpenSSH()
    {
        $this->sshData['openssh'] = false;
        return $this;
    }

    /**
     * @return int
     */
    public function getShellInABoxPort()
    {
        return isset($this->sshData['shellinabox_port']) && $this->sshData['shellinabox_port'] ? $this->sshData['shellinabox_port'] : 8021;
    }

    /**
     * @return bool
     */
    public function isShellInABoxRestricted()
    {
        return isset($this->sshData['shellinabox_restrict']) && (bool)$this->sshData['shellinabox_restrict'];
    }

    /**
     * @return bool
     */
    public function isShellInABoxEnabled()
    {
        return isset($this->sshData['shellinabox']) && $this->sshData['shellinabox'];
    }

    /**
     * @return bool
     */
    public function isOpenSSHEnabled()
    {
        return isset($this->sshData['openssh']) && $this->sshData['openssh'];
    }

    /**
     * @return null|string
     */
    public function getMaxSessionsCount()
    {
        return (int)$this->openSSH->get('MaxSessions');
    }

    /**
     * @return null|string
     */
    public function getPermitRootLogin()
    {
        return $this->openSSH->get('PermitRootLogin') === 'yes';
    }

    /**
     * @return null|string
     */
    public function getPermitTunnel()
    {
        return $this->openSSH->get('PermitTunnel') === 'yes';
    }

    /**
     * @return null|string
     */
    public function getLastLogPrinting()
    {
        return $this->openSSH->get('PrintLastLog') === 'yes';
    }

    /**
     * @return null|string
     */
    public function getTCPForwarding()
    {
        return $this->openSSH->get('AllowTcpForwarding') === 'yes';
    }

    /**
     * @return null|string
     */
    public function getX11Forwarding()
    {
        return $this->openSSH->get('X11Forwarding') === 'yes';
    }

    /**
     * @return null|string
     */
    public function getProtocol()
    {
        return (int)$this->openSSH->get('Protocol');
    }

    /**
     * @return null|string
     */
    public function getPort()
    {
        return (int)$this->openSSH->get('Port');
    }

    /**
     * @return null|string
     */
    public function getListenAddress()
    {
        return $this->openSSH->get('ListenAddress');
    }

    /**
     * Save
     */
    public function save()
    {
        $this->getDaemons()->put('SecureShellServer', $this->sshData, true);
        $this->openSSH->save();

        parent::save();
    }
}