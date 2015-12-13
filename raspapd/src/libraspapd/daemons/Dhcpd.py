#-*- encoding: utf-8 -*-

import re
import time

from BaseDaemon import BaseDaemon

class Dhcpd(BaseDaemon):
    """
        RaspAp
        --
        DHCP Server configuration
    """

    def start(self, interface, settings):
        """
        Execute all required commands

        :param interface:
        :param settings:
        :return:
        """

        self.interface = interface

        # at first setup an ip address for interface
        config = open('/etc/dhcpd/raspap/' + interface + '.conf').read()
        routerIP = re.findall(r'option routers ([0-9\.]+)', config)

        if not routerIP:
            self.app.logging.output('FATAL: Cannot read router ip address from DHCPD configuration for interface "' + interface + '"!', interface)
            return False

        result, output = self.app.executeCommand(['ifconfig', interface, routerIP[0], 'netmask', '255.255.255.0', 'up'])

        if not result:
            self.app.logging.output('Setting IP address on interface "' + interface + '" failed', interface)
            return False

        # and now run dhcpd server
        command = [
            'dhcpd', '-cf', '/etc/dhcpd/raspap/' + interface + '.conf', interface
        ]

        result, output = self.app.executeCommand(command)
        return result


    def finish(self):
        """
        Shutdown the dhcp server
        :return:
        """

        # ps aux |grep 'dhcpd -cf /etc/dhcpd/raspap/' + self.interface + ' | grep -v 'grep'
        return self.find_and_kill_process('dhcpd -cf /etc/dhcpd/raspap/' + self.interface, self.interface)