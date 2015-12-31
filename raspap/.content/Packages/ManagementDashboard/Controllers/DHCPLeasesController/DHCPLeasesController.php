<?php
namespace RaspAP\Packages\ManagementDashboard\Controllers;

use RaspAP\Components\Controller\AbstractAdministrationController;
use Panthera\Components\Controller\Response;
use RaspAP\Components\DHCPD\Leases;

/**
 * RaspAP
 * --
 * DHCPLeases Controller
 *
 * @package RaspAP\Packages\ManagementDashboard\Controllers
 */
class DHCPLeasesController extends AbstractAdministrationController
{
    /**
     * @API
     * @return Response
     */
    public function defaultAction()
    {
        return new Response([
            'leases' => new Leases(),
        ], 'Admin/DHCPLeases/Main.tpl');
    }
}