<?php
namespace RaspAP\Packages\ManagementDashboard\Controllers;

use Panthera\Classes\BaseExceptions\PantheraFrameworkException;
use RaspAP\Components\Controller\AbstractAdministrationController;
use Panthera\Components\Controller\Response;

use RaspAP\Components\HostAPD\HostAPDInterface;
use RaspAP\Components\LinuxNetworkStack\LinuxNetworkStack;
use RaspAP\Components\NetworkInterface\InterfacesList;
use RaspAP\Components\NetworkInterface\AbstractInterface;
use RaspAP\Components\NetworkInterface\WirelessInterface;

/**
 * RaspAP
 * --
 * Class ConfigureInterface
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 * @package RaspAP\Packages\ManagementDashboard\Controllers
 */
class ConfigureInterfaceController extends AbstractAdministrationController
{
    /** @var InterfacesList $interfaces */
    protected $interfaces;

    /** @var AbstractInterface|WirelessInterface $interface */
    protected $interface;

    /**
     * Process request for every action
     *
     * @param array $params
     * @param array $get
     * @param array $post
     *
     * @return \Panthera\Components\Controller\Request|void
     */
    public function processRequest($params, $get, $post)
    {
        parent::processRequest($params, $get, $post);

        $this->interfaces = new InterfacesList();
        $interfaces = $this->interfaces->getInterfaces();

        if (!isset($interfaces[$this->request->params('interface')]))
        {
            throw new \InvalidArgumentException('Interface not found');
        }

        $this->interface = $interfaces[$this->request->params('interface')];
    }

    /**
     * @API
     * @return Response
     */
    public function defaultAction()
    {
        $response = new Response([], 'Admin/ConfigureInterface/Main.tpl');
        $response->assign([
            'interfaces' => $this->interfaces->getInterfaces(),
            'interface' => $this->interface,
        ]);

        return $response;
    }

    /**
     * @API
     * @return Response
     */
    public function commitAction()
    {
        $response = new Response();
        $role = $this->request->post('InterfaceRole');

        if (!in_array($this->request->post('InterfaceRole'), $this->interface->getPossibleRoles()))
        {
            throw new \InvalidArgumentException('Interface of type "' . $this->interface->getType() . '" cannot be in a role of "' . htmlspecialchars($this->request->post('InterfaceRole')) . '"');
        }

        /**
         * Access Point
         */
        if ($role === 'access_point')
        {
            $hostAPD = new HostAPDInterface($this->interface);
            $hostAPD->setup(
                $this->request->post('AP_ESSID'),
                $this->request->post('AP_Mode'),
                intval($this->request->post('AP_Channel')),
                (bool)intval($this->request->post('AP_Hidden'))
            );

            $hostAPD->setEncryption(
                $this->request->post('AP_Encryption'),
                $this->request->post('AP_Passphrase'),
                $this->request->post('AP_Pairwise')
            );

            // bridge mode
            $hostAPD->bridgeWithInterfaces((array)$this->request->post('AP_BridgeInterfaces'));

            $hostAPD->setFrameProtection($this->request->post('AP_FrameProtection'));
            $hostAPD->setCountryCode($this->request->post('AP_CountryCode'));
            $hostAPD->setDFS($this->request->post('AP_DFS'));
            $hostAPD->save();
        }

        /**
         * Client: DHCP
         */
        elseif ($role === 'client_cable_dhcp')
        {
            $stack = $this->interface->getLinuxNetworkStack();
            $stack->setupAsDHCPClient();
            $stack->save();
        }

        /**
         * Client: Static address
         */
        elseif ($role === 'client_cable_static')
        {
            $stack = $this->interface->getLinuxNetworkStack();
            $stack->setupAsStaticClient(
                $this->request->post('CableStatic_IP', '!Classes/IP::address'),
                $this->request->post('CableStatic_Netmask', 'Classes/IP::address'),
                $this->request->post('CableStatic_Gateway', 'Classes/IP::address'),
                $this->request->post('CableStatic_Broadcast', 'Classes/IP::address')
            );
            $stack->save();
        }

        /**
         * Monitoring mode
         */
        elseif ($role === 'monitor')
        {
            $stack = new LinuxNetworkStack($this->interface);
            $stack->setupMonitorMode(
                $this->request->post('Monitor_Filter'),
                $this->request->post('Monitor_PacketType'),
                $this->request->post('Monitor_PacketSize'),
                true
            );
            $stack->save();
        }

        /**
         * Turning off the interface
         */
        elseif ($role === 'down')
        {
            $stack = new LinuxNetworkStack($this->interface);
            $stack->down();
            $stack->useInterface(!$this->request->post('Down_DontUse'));
            $stack->save();
        }

        $response->assign([
            'status' => true,
            'message' => 'Configuration saved, now wait for the server to refresh the live configuration',
        ]);

        return $response;
    }
}