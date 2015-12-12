<?php
namespace RaspAP\Components\Controller;
use Panthera\Components\Controller\BaseFrameworkController;

abstract class AbstractAdministrationController extends BaseFrameworkController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // @todo Validate permissions here
    }
}