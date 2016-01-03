<?php
namespace RaspAP\Packages\ShellConfigurator\Controllers;

use Panthera\Classes\BaseExceptions\InvalidArgumentException;
use Panthera\Classes\BaseExceptions\ValidationException;
use Panthera\Components\Controller\Response;
use RaspAP\Components\Controller\AbstractAdministrationController;
use RaspAP\Components\NetworkInterface\InterfacesList;
use RaspAP\Packages\ShellConfigurator\Classes\SSHEntity;

/**
 * RaspAP
 * ---------------
 * Controller SSHController
 */
class SSHController extends AbstractAdministrationController
{
    /** @var SSHEntity $entity */
    protected $entity;

    /**
     * @API
     * @return Response
     */
    public function defaultAction()
    {
        $this->entity = new SSHEntity();
        $message = $this->entity->getFailMessage();
        $response = new Response([
            'saved'        => false,
            'ssh'          => $this->entity,
            'interfaces'   => new InterfacesList(),
        ], 'SSHController.tpl');

        if ((string)$this->request->post('FormPosted') === 'true')
        {
            try
            {
                $this->handleSSHFormSave($response);
            }
            catch (ValidationException $e)
            {
                $message = 'Validation failed: ' . $e->getMessage();
            }
            catch (InvalidArgumentException $e)
            {
                $message = 'Invalid input: ' . $e->getMessage();
            }
            catch (\Exception $e)
            {
                $message = 'Got exception: ' . $e->getMessage();
            }
        }

        $response->assign('errorMessage', $message);
        return $response;
    }

    /**
     * Handle form submit
     *
     * @param Response $response
     */
    protected function handleSSHFormSave(Response $response)
    {
        // openssh
        $this->entity->setupOpenSSH(
            $this->request->post('SSH_OpenSSH_ListenAddress', '!Classes/IP::address'),
            $this->request->post('SSH_OpenSSH_Port', '!integer'),
            $this->request->post('SSH_OpenSSH_Protocol', '!integer'),
            $this->request->post('SSH_OpenSSH_X11Forwarding', 'integer'),
            $this->request->post('SSH_OpenSSH_TCPForwarding', 'integer'),
            $this->request->post('SSH_OpenSSH_LastLog', 'integer'),
            $this->request->post('SSH_OpenSSH_PermitTunnel', 'integer'),
            $this->request->post('SSH_OpenSSH_RootLogin', 'integer'),
            $this->request->post('SSH_OpenSSH_MaxSessions', '!integer')
        );

        if (!$this->request->post('SSH_OpenSSH'))
        {
            $this->entity->disableOpenSSH();
        }

        // shellinabox
        $this->entity->setupShellInABox(
            $this->request->post('SSH_WebShell_Port', '!integer'),
            $this->request->post('SSH_WebShell_RestrictAccess', 'integer')
        );

        if (!$this->request->post('SSH_WebShell'))
        {
            $this->entity->disableShellInABox();
        }

        $this->entity->save();
        $response->assign('saved', true);
    }

    /**
     * @API
     * @return Response
     */
    protected function openShellAction()
    {
        $this->entity = new SSHEntity();
        $url = parse_url($_SERVER['HTTP_HOST']);
        $domain = $url['host'];

        return new Response([
            'address' => 'https://' . $domain . ':' . $this->entity->getShellInABoxPort(),
        ], 'SSHConsole.tpl');
    }
}