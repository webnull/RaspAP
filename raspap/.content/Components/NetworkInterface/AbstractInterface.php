<?php
namespace RaspAP\Components\NetworkInterface;
use \Panthera\Components\Orm\ORMBaseFrameworkObject;
use Panthera\Components\Kernel\Framework;
use RaspAP\Components\LinuxNetworkStack\LinuxNetworkStack;
use RaspAP\Components\NetworkInterface\Helpers\InterfaceDaemons;

/**
 * RaspAP
 * --
 * Abstract interface for network interface classes
 *
 * @package RaspAP\Components\NetworkInterface
 */
abstract class AbstractInterface extends ORMBaseFrameworkObject
{
    protected static $__ORM_Table = 'interfaces';
    protected static $__ORM_IdColumn = 'id';

    /**
     * @orm
     * @column id
     * @var integer
     */
    public $interfaceId          = null;

    /**
     * @orm
     * @column name
     * @var string
     */
    public $interfaceName         = '';

    /**
     * @orm
     * @column role
     * @var string
     */
    protected $interfaceRole         = '';

    /**
     * @orm
     * @column type
     * @var string
     */
    public $interfaceType         = '';

    /**
     * @orm
     * @column daemons
     * @var string
     */
    public $interfaceDaemons         = '';

    /**
     * @orm
     * @column fail_message
     * @var string
     */
    protected $interfaceFailMessage = '';

    /**
     * @orm
     * @column last_updated
     * @var string
     */
    public $interfaceLastUpdated  = '';

    /**
     * @var InterfaceDaemons $daemonsList
     */
    protected $daemonsList;

    /**
     * @var LinuxNetworkStack $linuxNetworkStack
     */
    protected $linuxNetworkStack;

    /**
     * @var string $output
     */
    protected $output = '';

    /**
     * @var string[] $details
     */
    protected $details = [];

    /**
     * @param string $name Interface name
     * @param string $output ifconfig output
     */
    public function __construct($name, $output)
    {
        $this->interfaceRole = $this->getRole();
        $this->interfaceType = $this->getType();
        $this->interfaceName = $name;
        $this->output = $output;

        $this->parse();

        $data = Framework::getInstance()->database->query('SELECT * FROM interfaces WHERE name = :name', [
            'name' => $name,
        ]);

        if ($data && isset($data[0]))
        {
            parent::__construct($data[0]);
        }
        else
        {
            parent::__construct(null);
        }

        $this->daemonsList = new InterfaceDaemons($this->interfaceDaemons);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->interfaceName;
    }

    /**
     * Returns last failure message (if any)
     *
     * @return string
     */
    public function getFailMessage()
    {
        return $this->interfaceFailMessage;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole($role)
    {
        if (!in_array($role, $this->getPossibleRoles()))
        {
            throw new \InvalidArgumentException('Invalid role "' . $role . '" selected');
        }

        $this->interfaceRole = $role;
        return $this;
    }

    /**
     * Get list of all possible roles to select
     *
     * @return array
     */
    public function getPossibleRoles()
    {
        return [
            'down',
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->getName() === 'lo')
        {
            return 'Loopback';
        }

        $parts = explode('\\', get_called_class());
        return str_replace('Interface', '', end($parts));
    }

    /**
     * @override
     * @return bool
     */
    public function isAccessPoint()
    {
        return false;
    }

    /**
     * Search a match using regular expressions and save match to an array
     *
     * @param string $regex
     * @param string $name
     * @param int $index Result index from preg_match
     */
    public function extractData($regex, $name, $index = 2)
    {
        $this->details[$name] = null; // null = not found
        preg_match($regex, $this->output, $results);

        if (is_array($results) && $results && isset($results[$index]))
        {
            $this->details[$name] = $results[$index];
        }
    }

    /**
     * Return diagnostic messages for interface
     *
     * @return string
     */
    public function getDiagnosticMessages()
    {
        return shell_exec('dmesg |grep ' . $this->getName());
    }

    /**
     * Get information about interface
     *
     * @return \string[]
     */
    public function getInfo()
    {
        return $this->details;
    }

    /**
     * @return \string[]
     */
    public function __exposePublic()
    {
        return $this->details;
    }

    /**
     * Get interface role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->interfaceRole ? $this->interfaceRole : 'down';
    }

    /**
     * Parses output of shell commands
     */
    abstract function parse();

    /**
     * Check if interface is active
     *
     * @return bool
     */
    abstract function isConnected();

    /**
     * Save the entity
     */
    public function save()
    {
        $this->interfaceDaemons = $this->daemonsList->__toString();
        $this->interfaceLastUpdated = date('Y-m-d H:i:s');
        parent::save();
    }

    /**
     * @return LinuxNetworkStack
     */
    public function getLinuxNetworkStack()
    {
        if (!$this->linuxNetworkStack)
        {
            $this->linuxNetworkStack = new LinuxNetworkStack($this);
        }

        return $this->linuxNetworkStack;
    }

    /**
     * @return InterfaceDaemons
     */
    public function getDaemons()
    {
        return $this->daemonsList;
    }
}