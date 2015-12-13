#-*- encoding: utf-8 -*-
import re
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

    def start(self, interface, settings):
        """
        Execute all required commands

        :param interface:
        :param settings:
        :return:
        """

        result, output = self.app.executeCommand('route | grep "default"', shell=True)
        route = re.findall('.*(\s+?)([a-z0-9]+)', output)

        if not route or not len(route) or len(route[0]) < 1:
            self.app.logging.output('Cannot find default route from output of "route | grep \'default\'"', interface)
            self.gatewayInterface = 'lo'
        else:
            self.gatewayInterface = route[0][1]

        self.interface = interface

        results = [
            # execute additional iptables rules (custom user rules)
            #self.app.executeCommand('bash /etc/raspap-iptables.sh', shell=True) if os.path.isfile('/etc/raspap-iptables.sh') else True,

            # kernel settings - ip forwarding
            self.app.executeCommand('echo 1 > /proc/sys/net/ipv4/ip_forward', shell=True),
            self.app.executeCommand(['sysctl', '-w', 'net.ipv4.ip_forward=1']),
        ]

        for result in results:
            if not result[0]:
                return False

        for rule in self.natRules:
            rule = rule.replace('%interface', interface).replace('%gw', self.gatewayInterface)

            # remove a rule
            self.app.executeCommand(rule.replace('%A', '-D'), shell=True)

            # add it again
            self.app.executeCommand(rule.replace('%A', '-A'), shell=True)

        return True


    def finish(self):
        """
        Clean up rules after exiting
        :return:
        """

        for rule in self.natRules:
            # remove a rule
            rule = rule.replace('%A', '-D').replace('%interface', self.interface).replace('%gw', self.gatewayInterface)

            self.app.executeCommand(rule, shell=True)