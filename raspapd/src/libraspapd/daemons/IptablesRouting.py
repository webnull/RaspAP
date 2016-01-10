#-*- encoding: utf-8 -*-
import re
import os
from BaseDaemon import BaseDaemon

class IptablesRouting(BaseDaemon):
    """
        RaspAp
        --
        Configures kernel and routing
    """

    gatewayInterface = ''

    natRules = [
        # routing - gateway interface <==> our interface
        # gateway interface is the interface connected to the internet
        'iptables -t nat %A POSTROUTING -o %gw -j MASQUERADE',
        'iptables %A FORWARD -i %gw -o %interface -m state --state RELATED,ESTABLISHED -j ACCEPT',
        'iptables %A FORWARD -i %interface -o %gw -j ACCEPT'
    ]

    bridgeRules = [

    ]

    def detect_gateway(self):
        """
        Detect which interface provides access to internet

        :return:
        """

        result, output = self.app.executeCommand('route | grep "default"', shell=True)
        route = re.findall('.*(\s+?)([a-z0-9]+)', output)

        if not route or not len(route) or len(route[0]) < 1:
            self.app.logging.output('Cannot find default route from output of "route | grep \'default\'"', self.interface)
            self.gatewayInterface = 'lo'
        else:
            self.app.logging.output('Found default gateway interface - ' + str(route[0][1]), self.interface)
            self.gatewayInterface = route[0][1]

        if not os.path.isfile('/sys/class/net/' + self.gatewayInterface):
            self.app.logging.output('Bug: Invalid gateway interface detected! gatewayInterface=' + self.gatewayInterface, self.interface)


    def setup_bridge(self, settings):
        """
        Set up a bridge connection

        :param settings:
        :return:
        """

        if "bridge" in settings and settings['bridge']:
            bridge = self.getBridgeForTheInterface()

            if bridge:
                self.interface = bridge

                self.app.logging.output('Configured to work as a bridge, creating a bridge then', interface)
                self.app.logging.output('Changing interface to ' + str(self.interface))

                self.bridgeRules = [
                    'brctl addbr ' + self.interface,
                    'brctl addif ' + self.interface + ' ' + ' '.join(settings['bridge'])
                ]


    def start(self, interface, settings):
        """
        Execute all required commands

        :param interface:
        :param settings:
        :return:
        """

        self.interface = interface
        self.detect_gateway()
        self.setup_bridge(settings)

        results = [
            # kernel settings - ip forwarding
            self.app.executeCommand('echo 1 > /proc/sys/net/ipv4/ip_forward', shell=True),
            self.app.executeCommand([ 'sysctl', '-w', 'net.ipv4.ip_forward=1' ]),
        ]

        for result in results:
            if not result[0]:
                return False

        for rule in (self.bridgeRules + self.natRules):
            rule = rule.replace('%interface', self.interface).replace('%gw', self.gatewayInterface)
            logging = True

            # remove a rule
            if "iptables " in rule:
                self.app.logging.output('Removing previous rule (if any): ' + rule.replace('%A', '-D'), interface)
                self.app.executeCommand(rule.replace('%A', '-D'), shell=True, logging = False)

            # add it again

            if not "iptables " in rule:
                logging = False

            self.app.executeCommand(rule.replace('%A', '-A'), shell=True, logging = logging)

        return True


    def finish(self):
        """
        Clean up rules after exiting
        :return:
        """

        for rule in (list(reversed(self.bridgeRules)) + self.natRules):
            # bridges
            rule = rule.replace('brctl addbr ', 'brctl delbr ')
            rule = rule.replace('brctl addif ', 'brctl delif ')

            # remove a rule
            rule = rule.replace('%A', '-D').replace('%interface', self.interface).replace('%gw', self.gatewayInterface)

            self.app.executeCommand(rule, shell=True)

