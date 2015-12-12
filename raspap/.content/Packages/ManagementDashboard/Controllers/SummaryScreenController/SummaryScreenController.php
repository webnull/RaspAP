<?php
namespace RaspAP\Packages\ManagementDashboard\Controllers;

use RaspAP\Classes\DaemonUtils;
use \RaspAP\Components\Controller\AbstractAdministrationController;
use \Panthera\Components\Controller\Response;

/**
 * RaspAP, a wireless router from your computer
 * Summary Screen controller
 *
 * @package RaspAP\Packages\ManagementDashboard\Controllers
 */
class SummaryScreenController extends AbstractAdministrationController
{
    /**
     * @API
     * @return Response
     */
    public function defaultAction()
    {
        $list = new \RaspAP\Components\NetworkInterface\InterfacesList();
        $interfaces = $list->getInterfaces();

        return new Response([
            'interfaces' => $interfaces,
            'daemonNotRunningAlert' => !DaemonUtils::isTurnedOn(),
        ], 'Admin/SummaryScreen/Main.tpl');
    }
}