<?php
namespace RaspAP\Packages\BasePackage\Controllers;

use Panthera\Packages\BasePackage\Controllers\IndexController as ParentController;
use Panthera\Components\Controller\ResponseText;

/**
 * RaspAP
 * --
 * Class IndexController
 *
 * @package RaspAP\Packages\BasePackage\Controllers
 */
class IndexController extends ParentController
{
    /**
     * @return ResponseText
     */
    public function defaultAction()
    {
        header('Location: login');
        exit;
    }
}