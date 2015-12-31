<?php
namespace RaspAP\Packages\ManagementDashboard\Controllers;

use Panthera\Components\Controller\BaseFrameworkController;
use Panthera\Components\Controller\Response;

/**
 * RaspAP
 * --
 * LoginController
 *
 * @package RaspAP\Packages\ManagementDashboard\Controllers
 */
class LoginController extends BaseFrameworkController
{
    /**
     * @return Response
     */
    public function defaultAction()
    {
        $message = '';

        if ($this->request->post('login') && $this->request->post('password'))
        {
            try
            {
                $result = $this->app->component->auth->checkAccess(
                    $this->request->post('login'),
                    $this->request->post('password')
                );

                if ($result)
                {
                    $this->app->component->auth->grantAccess($this->request->post('login'), true);
                    header('Location: summary');
                    exit;
                }
                else
                {
                    $message = 'Invalid login or password';
                }
            }
            catch (\Exception $e)
            {
                $message = $e->getMessage();
            }
        }

        return new Response([
            'message' => $message,
        ], 'Admin/Login.tpl');
        //$this->app->component->auth->grantAccess();
    }

    /**
     * Logout
     *
     * @API
     */
    public function logoutAction()
    {
        if ($this->request->post('logout') == $this->app->session->getSessionId())
        {
            $this->app->session->clear();
            header('Location: login');
            exit;
        }

        return new Response();
    }
}