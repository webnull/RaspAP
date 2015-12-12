<?php
namespace RaspAP\Components\StartupComponent;

use \Panthera\Components\StartupComponent\StartupComponent as DummyStartupComponent;

/**
 * RaspAP
 * --
 * StartupComponent
 *
 * @package RaspAP\Components\StartupComponent
 */
class StartupComponent extends DummyStartupComponent
{
    /**
     * Executes after framework's setup()
     */
    public function afterFrameworkSetup()
    {
        /** @var \Rain\RainTPL4 $rainTPL */
        $rainTPL = $this->app->template->rain;

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