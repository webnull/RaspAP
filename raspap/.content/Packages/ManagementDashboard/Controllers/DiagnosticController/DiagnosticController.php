<?php
namespace RaspAP\Packages\ManagementDashboard\Controllers;

use RaspAP\Components\Controller\AbstractAdministrationController;
use Panthera\Components\Controller\Response;
use RaspAP\Components\NetworkInterface\InterfacesList;

/**
 * RaspAP
 * --
 * Class DiagnosticController
 *
 * @package RaspAP\Packages\ManagementDashboard\Controllers
 */
class DiagnosticController extends AbstractAdministrationController
{
    /**
     * List of all available commands to execute
     *
     * @var array $commands
     */
    protected $commands = [
        'System version'  => 'uname -a',
        'sysctl options'  => 'sysctl -a',
        'Kernel modules'  => 'lsmod',
        'Processes'       => 'ps aux',
        'PCI devices'     => 'lspci -v',
        'USB devices'     => 'lsusb -v',
        'Kernel messages' => 'dmesg',
        'ifconfig'        => 'ifconfig -a',
        'iwconfig'        => 'iwconfig',
        'iw list'         => 'iw list',
        'iptables'        => 'iptables -S',
        'NAT routing'     => 'iptables -t nat -S',
        'Routing'         => 'route',
        'Bridges'         => 'brctl show',
        'HostAPD version' => 'hostapd -v',
        'DHCPD version'   => 'dhcpd -h',
        'PHP version'     => 'php -v',
        'Python 2 version' => 'python2 --version',
    ];

    /**
     * Execute a command grabbing output from both stdout and stderr
     *
     * @param string $command
     * @return string
     */
    protected function executeCommand($command)
    {
        $descriptors = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"],  // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes, dirname(__FILE__), null);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // add space to every line ending (little bit hack for regular expressions)
        $output = $stdout . $stderr;
        $tmp = explode("\n", $output);
        $output = implode("\n", array_map(function ($line) { return $line . ' '; }, $tmp));

        return $output;
    }

    /**
     * @return Response
     */
    public function defaultAction()
    {
        $response = '';
        $command = $this->request->get('command');

        if ($command && isset($this->commands[$command]))
        {
            $response = $this->executeCommand($this->commands[$command]);

            if (!$response)
            {
                $response = 'Command returned no output, probably user have no access to execute it in operating system';
                $response .= ', current user is: ' . shell_exec('whoami');
            }
        }

        return new Response([
            'commands' => $this->commands,
            'response' => $this->underlineKeywordsInResponse($response),
            'selectedCommand' => $command,
        ], 'Admin/Diagnostic/Main.tpl');
    }

    /**
     * Underline important keywords in commands output response
     * eg. interface names
     *
     * @param string $response
     * @return string
     */
    protected function underlineKeywordsInResponse($response)
    {
        $interfaces = new InterfacesList();
        $interfaceNames = array_map(function($interface) { return $interface->getName(); }, $interfaces->getInterfaces());
        $interfaceNames = implode('|', $interfaceNames);

        $replacement = '<b><i>$0</i></b>';
        $response = preg_replace('/([\[\(\. ]?)(' . $interfaceNames . ')([\t \.:])/i', $replacement, $response);

        // additional keywords replacement
        $additionalKeywords = [
            'net.ipv4',
            'net.ipv6',
            'Network controller:',
            'ath9k',
            'ath5k',
            'r8169',
            'rt2800usb',
            'rt2x00usb',
            'rt2800lib',
            'ath3k',
            'rt2x00lib',
            'Ethernet controller:',
            'cfg80211',
            'NET:',
            'ieee80211',
            'IPv6:',
            'ath:',
            'bridge:',
            'associate with',
            'associated ',
            'authenticate with ',
            'Supported interface modes:',
            '* AP ',
            '* AP/VLAN ',
            '* TKIP',
            'Supported Ciphers:',
            'Frequencies:',
            '* monitor ',
            'Wireless Adapter ',
            'Ralink Technology',
            'Atheros Communications, Inc.',
            ' Wireless ',
            '/dhcpcd',
            'dhcpd -cf /etc/dhcpd/raspap/',
            'hostapd /etc/hostapd/raspap/',
        ];

        foreach ($additionalKeywords as $keyword)
        {
            $response = str_replace($keyword, str_replace('$0', $keyword, $replacement), $response);
        }

        return $response;
    }
}