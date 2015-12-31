<?php
$appIndex = array (
  'path_translations' => 
  array (
    0 => 'packages/admin/dashboard/translations',
  ),
  'autoloader' => 
  array (
    '\\IndexControllerTest' => '$LIB$/Packages/BasePackage/Tests/IndexControllerTest.php',
    '\\Panthera\\Packages\\BasePackage\\Controllers\\ErrorNotFoundController' => '$LIB$/Packages/BasePackage/Controllers/ErrorNotFoundController/ErrorNotFoundController.php',
    '\\RaspAP\\Packages\\BasePackage\\Controllers\\IndexController' => '$APP$.content/Packages/BasePackage/Controllers/IndexController/IndexController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\ConfigureInterfaceController' => '$APP$.content/Packages/ManagementDashboard/Controllers/ConfigureInterfaceController/ConfigureInterfaceController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\DHCPLeasesController' => '$APP$.content/Packages/ManagementDashboard/Controllers/DHCPLeasesController/DHCPLeasesController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\DiagnosticController' => '$APP$.content/Packages/ManagementDashboard/Controllers/DiagnosticController/DiagnosticController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\LoginController' => '$APP$.content/Packages/ManagementDashboard/Controllers/LoginController/LoginController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\ManagePasswordsController' => '$APP$.content/Packages/ManagementDashboard/Controllers/ManagePasswordsController/ManagePasswordsController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\SummaryScreenController' => '$APP$.content/Packages/ManagementDashboard/Controllers/SummaryScreenController/SummaryScreenController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\TORController' => '$APP$.content/Packages/ManagementDashboard/Controllers/TORController/TORController.php',
    '\\RaspAP\\Packages\\Test\\Controllers\\MyTestControllerController' => '$APP$.content/Packages/Test/Controllers/MyTestControllerController/MyTestControllerController.php',
  ),
  'signals' => 
  array (
    'UI.Admin.template.menu' => 
    array (
      0 => 
      array (
        'type' => 'signal',
        'call' => '\\dashboardModule::attachToAdminMenu',
        'file' => '$LIB$/Packages/admin/dashboard/classes/dashboardModule.class.php',
      ),
    ),
  ),
  'Routes' => 
  array (
    '`^/configureInterface,(?:(?P<interface>[^/\\.]++))$`' => 
    array (
      'matches' => 
      array (
        0 => 
        array (
          0 => '[:interface]',
          1 => '',
          2 => '',
          3 => 'interface',
          4 => '',
        ),
      ),
      'controller' => '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\ConfigureInterfaceController',
      'original' => '/configureInterface,[:interface]',
      'methods' => 'GET|POST',
      'priority' => 901,
    ),
    '`^/dhcpConnectedDevices$`' => 
    array (
      'matches' => 
      array (
      ),
      'controller' => '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\DHCPLeasesController',
      'original' => '/dhcpConnectedDevices',
      'methods' => 'GET|POST',
      'priority' => 900,
    ),
    '`^/diagnostic$`' => 
    array (
      'matches' => 
      array (
      ),
      'controller' => '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\DiagnosticController',
      'original' => '/diagnostic',
      'methods' => 'GET|POST',
      'priority' => 880,
    ),
    '`^/login$`' => 
    array (
      'matches' => 
      array (
      ),
      'controller' => '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\LoginController',
      'original' => '/login',
      'methods' => 'GET|POST',
      'priority' => 800,
    ),
    '`^/managePasswords,(?:(?P<interface>[^/\\.]++))$`' => 
    array (
      'matches' => 
      array (
        0 => 
        array (
          0 => '[:interface]',
          1 => '',
          2 => '',
          3 => 'interface',
          4 => '',
        ),
      ),
      'controller' => '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\ManagePasswordsController',
      'original' => '/managePasswords,[:interface]',
      'methods' => 'GET|POST',
      'priority' => 800,
    ),
    '`^/summary$`' => 
    array (
      'matches' => 
      array (
      ),
      'controller' => '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\SummaryScreenController',
      'original' => '/summary',
      'methods' => 'GET|POST',
      'priority' => 999,
    ),
    '`^/anonymitySettings$`' => 
    array (
      'matches' => 
      array (
      ),
      'controller' => '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\TORController',
      'original' => '/anonymitySettings',
      'methods' => 'GET|POST',
      'priority' => 999,
    ),
    '`^/example,(?:(?P<myVariable>[^/\\.]++))$`' => 
    array (
      'matches' => 
      array (
        0 => 
        array (
          0 => '[:myVariable]',
          1 => '',
          2 => '',
          3 => 'myVariable',
          4 => '',
        ),
      ),
      'controller' => '\\RaspAP\\Packages\\Test\\Controllers\\MyTestControllerController',
      'original' => '/example,[:myVariable]',
      'methods' => 'GET|POST',
      'priority' => 100,
    ),
  ),
  'packages' => 
  array (
    0 => 'BasePackage',
    1 => 'ManagementDashboard',
    2 => 'Test',
  ),
);