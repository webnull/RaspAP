<?php
namespace RaspAP\Packages\ManagementDashboard\Controllers;

use Panthera\Classes\BaseExceptions\InvalidArgumentException;
use RaspAP\Components\Controller\AbstractAdministrationController;
use Panthera\Components\Controller\Response;

use RaspAP\Components\NetworkInterface\InterfacesList;
use RaspAP\Components\NetworkInterface\AbstractInterface;
use RaspAP\Components\NetworkInterface\WirelessInterface;
use RaspAP\Components\HostAPD\PSKCollection;

class ManagePasswordsController extends AbstractAdministrationController
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
        $psk = $this->interface->getWPAPassphrases();

        return new Response([
            'psk'             => $psk,
            'interface'       => $this->interface,
            'managePasswords' => true,
            'statusMessage'   => '',
        ], 'Admin/ConfigureInterface/ManagePasswords.tpl');
    }

    /**
     * @API
     * @return Response
     */
    public function addMachineAction()
    {
        $response = $this->defaultAction();

        /** @var PSKCollection $psk */
        $psk = $response->getVariable('psk');

        try
        {
            $psk->setUser($this->request->post('PSK_Mac'), $this->request->post('PSK_Secr'), $this->request->post('PSK_Title'));
            $psk->save();

            $response->assign('statusMessage', '');
            $response->assign('redirect', true);
        }
        catch (InvalidArgumentException $exception)
        {
            $response->assign('statusMessage', $exception->getMessage());
        }
        catch (\UnexpectedValueException $exception)
        {
            $response->assign('statusMessage', $exception->getMessage());
        }

        return $response;
    }

    /**
     * Remove machine from list of Mac Addresses
     *
     * @API
     * @return Response
     */
    public function removeMachineAction()
    {
        // extending default action
        $response = $this->defaultAction();

        /** @var PSKCollection $psk */
        $psk = $response->getVariable('psk');

        try
        {
            $psk->removeUser($this->request->post('PSK_Mac'));
            $psk->save();

            $response->assign('statusMessage', '');
        }
        catch (\Exception $e)
        {
            $response->assign('statusMessage', $e->getMessage());
        }

        return $response;
    }
}