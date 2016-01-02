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

    originInterface = ''

    def start(self, interface, settings):
        """
        Execute all required commands

        :param interface:
        :param settings:
        :return:
        """

        self.originInterface = interface
        self.interface = interface
        bridge = self.getBridgeForTheInterface()

        # at first setup an ip address for interface
        config = open('/etc/dhcpd/raspap/' + self.originInterface + '.conf').read()
        routerIP = re.findall(r'option routers ([0-9\.]+)', config)

        if not routerIP:
            self.app.logging.output('FATAL: Cannot read router ip address from DHCPD configuration for interface "' + interface + '"!', interface)
            return False

        if bridge:
            self.app.logging.output('Setting 0.0.0.0 address on ' + interface + ' and configuring bridge on ' + bridge, interface)
            result, output = self.app.executeCommand(['ifconfig', interface, '0.0.0.0', 'up'])
            self.interface = interface = bridge


        result, output = self.app.executeCommand(['ifconfig', self.interface, routerIP[0], 'up'])

        if not result:
            self.app.logging.output('Setting IP address on interface "' + self.interface + '" failed', self.interface)
            return False


        # and now run dhcpd server
        command = [
            'dhcpd', '-cf', '/etc/dhcpd/raspap/' + self.originInterface + '.conf', self.interface
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