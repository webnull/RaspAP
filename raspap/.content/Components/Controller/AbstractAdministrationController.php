<?php
namespace RaspAP\Components\Controller;
use Panthera\Components\Controller\BaseFrameworkController;

/**
 * RaspAP
 * --
 * AbstractAdministrationController
 *
 * @package RaspAP\Components\Controller
 */
abstract class AbstractAdministrationController extends BaseFrameworkController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        /** @var \RaspAP\Components\Auth\AuthController $auth */
        $auth = $this->app->component->auth;

        if (!$auth->isAuthorized())
        {
            header('Location: login');
            exit;
        }
    }
}