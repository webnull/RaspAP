<?php
namespace RaspAP\Packages\ManagementDashboard\Controllers;

use Panthera\Classes\BaseExceptions\ValidationException;
use RaspAP\Components\Controller\AbstractAdministrationController;
use Panthera\Components\Controller\Response;
use RaspAP\Components\NetworkInterface\InterfacesList;
use RaspAP\Components\TOR\Privoxy;
use RaspAP\Components\TOR\TorConfiguration;
use RaspAP\Components\TOR\TorEntity;

/**
 * RaspAP
 * --
 * Class TORController
 *
 * @package RaspAP\Packages\ManagementDashboard\Controllers
 */
class TORController extends AbstractAdministrationController
{
    /**
     * @API
     * @return Response
     */
    public function defaultAction()
    {
        $entity = new TorEntity();
        $configuration = new TorConfiguration();

        return new Response([
            'interfaces' => new InterfacesList(),
            'TORConfiguration' => $configuration,
            'TOREntity' => $entity,
            'Privoxy'   => new Privoxy(),
        ], 'Admin/TOR/Main.tpl');
    }

    /**
     * @param TorEntity $entity
     * @param Response $response
     *
     * @throws ValidationException
     */
    protected function setupProxy(TorEntity $entity, Response $response)
    {
        if ($this->request->post('TOR_UserProxy', 'boolean'))
        {
            $entity->getDaemons()->put('privoxy', true);

            $split = explode(':', $this->request->post('TOR_ProxyBindAddress'));

            if (count($split) !== 2)
            {
                throw new ValidationException('Please specify a valid bind IP address', 'PRIVOXY_INVALID_IP_ADDRESS');
            }

            $proxy = new Privoxy();
            $proxy->setupTOR(
                $split[0],
                $split[1]
            );
            $proxy->save();
            $response->assign('Privoxy', $proxy);
        }
        else
        {
            $entity->getDaemons()->clear('privoxy');
        }
    }

    /**
     * TOR Bridge setup
     *
     * @API
     * @throws \Panthera\Classes\BaseExceptions\InvalidArgumentException
     * @return Response
     */
    public function setupBridgeAction()
    {
        /** @var Response $response */
        $response = $this->defaultAction();

        /** @var TorEntity $entity */
        $entity = $response->variables['TOREntity'];

        try
        {
            $configuration = new TorConfiguration();
            $configuration->configureBridge();

            // commit change to database
            $entity->enable('bridge');
            $this->setupProxy($entity, $response);
            $entity->save();

            // update response
            $response->assign('TOREntity', $entity);
            $response->assign('TORConfiguration', $configuration);
        }
        catch (\Exception $e)
        {
            return $response->assign('status', $e->getMessage());
        }

        $response->assign('status', true);
        return $response;
    }

    /**
     * Setup a TOR Relay/Exit node
     *
     * @API
     * @throws \Panthera\Classes\BaseExceptions\InvalidArgumentException
     * @return Response
     */
    public function setupRelayAction()
    {
        /** @var Response $response */
        $response = $this->defaultAction();

        /** @var TorEntity $entity */
        $entity = $response->variables['TOREntity'];

        try
        {
            // allowed ports on exit node (if any)
            $ports = str_replace(' ', '', $this->request->post('TOR_ExitPolicy', 'regexp@[^[0-9\, ]'));

            if ($ports)
            {
                $ports = explode(',', $ports);
            }

            $configuration = new TorConfiguration();
            $configuration->configureRelay(
                $this->request->post('TOR_Address', '!Classes/IP::address'),
                $this->request->post('TOR_Nickname', '!regexp@[^A-Za-z0-9\-\_\.]'),
                $this->request->post('TOR_Rate', '!integer'),
                $this->request->post('TOR_BurstRate', '!integer'),
                $ports
            );

            // commit change to database
            $entity->enable('relay');
            $this->setupProxy($entity, $response);
            $entity->save();

            // update response
            $response->assign('TOREntity', $entity);
            $response->assign('TORConfiguration', $configuration);
        }
        catch (\Exception $e)
        {
            return $response->assign('status', $e->getMessage());
        }

        $response->assign('status', true);
        return $response;
    }

    /**
     * @API
     * @return Response
     */
    public function disableTORAction()
    {
        /** @var Response $response */
        $response = $this->defaultAction();

        try
        {
            /** @var TorEntity $entity */
            $entity = $response->variables['TOREntity'];
            $entity->disable();
            $entity->save();

            // update response
            $response->assign('TOREntity', $entity);
        }
        catch (\Exception $e)
        {
            return $response->assign('status', $e->getMessage());
        }

        return $response;
    }
}