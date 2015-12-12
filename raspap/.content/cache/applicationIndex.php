<?php
$appIndex = array (
  'path_translations' => 
  array (
    0 => 'packages/admin/dashboard/translations',
  ),
  'autoloader' => 
  array (
    '\\Panthera\\Packages\\admin\\dashboard\\DashboardController' => '$LIB$/Packages/admin/dashboard/controllers/DashboardController/DashboardController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\ConfigureInterfaceController' => '$APP$.content/Packages/ManagementDashboard/Controllers/ConfigureInterfaceController/ConfigureInterfaceController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\DHCPLeasesController' => '$APP$.content/Packages/ManagementDashboard/Controllers/DHCPLeasesController/DHCPLeasesController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\DiagnosticController' => '$APP$.content/Packages/ManagementDashboard/Controllers/DiagnosticController/DiagnosticController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\ManagePasswordsController' => '$APP$.content/Packages/ManagementDashboard/Controllers/ManagePasswordsController/ManagePasswordsController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\SummaryScreenController' => '$APP$.content/Packages/ManagementDashboard/Controllers/SummaryScreenController/SummaryScreenController.php',
    '\\RaspAP\\Packages\\ManagementDashboard\\Controllers\\TORController' => '$APP$.content/Packages/ManagementDashboard/Controllers/TORController/TORController.php',
    '\\dashboardModule' => '$LIB$/Packages/admin/dashboard/classes/dashboardModule.class.php',
  ),
  'signals' => 
  array (
    'UI.Admin.template.menu' => 
    array (
      0 => 
      array (
        'type' => 'signal',
        'call' => '\\dashboardModule::attachToAdminMenu',
        'file' => '$LIB$/packages/admin/dashboard/classes/dashboardModule.class.php',
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
  ),
  'packages' => 
  array (
    0 => 'ManagementDashboard',
  ),
);