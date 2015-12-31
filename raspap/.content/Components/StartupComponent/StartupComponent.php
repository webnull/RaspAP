<?php
namespace RaspAP\Components\StartupComponent;

use Panthera\Components\Kernel\Framework;
use \Panthera\Components\StartupComponent\StartupComponent as DummyStartupComponent;
use RaspAP\Components\Auth\AuthController;

/**
 * RaspAP
 * --
 * StartupComponent
 *
 * @package RaspAP\Components\StartupComponent
 */
class StartupComponent extends DummyStartupComponent
{
    /** @var AuthController $auth */
    public $auth;

    /**
     * Executes after framework's setup()
     */
    public function afterFrameworkSetup()
    {
        $this->auth = new AuthController();

        /** @var \Rain\RainTPL4 $rainTPL */
        $rainTPL = $this->app->template->rain;
        $rainTPL->assign('sessionId', $this->app->session->getSessionId());

        /**
         * Creates a modifier that allows attaching new javascript files
         *
         * @param string $path
         * @return string
         */
        $rainTPL->registerModifier('addJS', function ($path) use ($rainTPL) {
            $list = is_array($rainTPL->getAssignedVariable('includedJS')) ? $rainTPL->getAssignedVariable('includedJS') : [];

            if (!in_array($path, $list))
            {
                $list[] = $path;
            }

            $rainTPL->assign('includedJS', $list);
            return '';
        });
    }
}