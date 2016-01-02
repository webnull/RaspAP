#-*- encoding: utf-8 -*-
import subprocess
import sys
import time
import pantheradesktop.tools
from BaseDaemon import BaseDaemon

class NetworkStack(BaseDaemon):
    lastErrorMessage = ''
    tcpDumpSettings = {}
    interface = ''

    thread = None
    worker = None
    process = None

    def start(self, interface, settings):
        """
        Constructor

        :param str interface:
        :param Dict settings:
        :return: networkStack
        """

        if "client_dhcp" in settings:
            return self.run_dhcp_client(interface, settings)

        elif "client_static" in settings:
            return self.run_static_client(interface, settings)

        elif "down_interface" in settings:
            if not settings['useInterface']:
                self.app.logging.output('Skipping interface ' + interface, interface)
                return True

            return self.bring_down_interface(interface)

        elif "monitor_filter" in settings:
            return self.configure_monitoring(interface, settings)


    def run_static_client(self, interface, settings):
        """
        Configure interface to connect to network using static ip address

        :param interface:
        :param settings:
        :return:
        """

        commands = [
            'ifconfig ' + interface + ' down'
        ]

        if 'netmask' in settings and settings['netmask']:
            commands.append('ifconfig ' + interface + ' netmask ' + settings['netmask'])

        if 'gateway' in settings and settings['gateway']:
            commands.append('ifconfig ' + interface + ' gateway ' + settings['gateway'])

        if 'broadcast' in settings and settings['broadcast']:
            commands.append('ifconfig ' + interface + ' broadcast ' + settings['broadcast'])

        commands.append('ifconfig ' + interface + ' ' + settings['address'])
        commands.append('ifconfig ' + interface + ' up')

        for command in commands:
            if not self.app.executeCommand(command, shell=True)[0]:
                return False

        return True


    def run_dhcp_client(self, interface, settings):
        """
        Run "dhclient" on interface (ISC DHCP Client)

        :param interface:
        :param settings:
        :return:
        """

        status, output = self.app.executeCommand(['dhclient', interface])

        if not status:
            if not output:
                output = 'dhclient process finished with wrong code, no ip address assigned'

            self.lastErrorMessage = output

        return status


    def configure_monitoring(self, interface, settings):
        """
        Configure packets monitoring on interface

        :param interface:
        :param settings:
        :return: bool
        """

        # bring up the interface first
        status, output = self.app.executeCommand(['ifconfig', interface, 'up'])

        if not status:
            self.lastErrorMessage = output
            return False


        # setup "monitoring" mode on interface, so it could listen on all WiFi channels
        if settings['monitor_setupInterface']:
            status, output = self.app.executeCommand(['iwconfig', interface, 'mode', 'monitor'])

            if not status:
                self.lastErrorMessage = output
                return False

        # setup tcpdump
        self.tcpDumpSettings = settings
        self.interface = interface
        self.thread, self.worker = pantheradesktop.tools.createThread(self.runTcpDump)

        time.sleep(5) # wait for tcpdump to start, 5 seconds should be enough even on slower machines
        return (self.lastErrorMessage == '')


    def runTcpDump(self, thread):
        """
        Run tcpdump in background

        For security reasons we do not allow to specify "-w" parameter value from web panel
        as it could point for example to an existing file like /bin/bash, or a low space disk that could be overflowed

        :return:
        """

        options = [
            'tcpdump', '-i', self.interface, '-w', '/tmp/raspap-' + self.interface + '.pcap'
        ]

        if self.app.commandsPrefix:
            options = self.app.commandsPrefix + options

        if self.tcpDumpSettings['monitor_packetTypes']:
            packetTypes = []

            for type in self.tcpDumpSettings['monitor_packetTypes']:
                packetTypes.append(type)

            options = options + ['-v', " or ".join(packetTypes).lower()]

        if self.tcpDumpSettings['monitor_packetSize']:
            options = options + ['-s', str(self.tcpDumpSettings['monitor_packetSize'])]

        if self.tcpDumpSettings['monitor_filter']:
            options = options + [str(self.tcpDumpSettings['monitor_filter'])]

        self.app.logging.output(str(options))
        self.process = subprocess.Popen(options, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

        while True:
            self.process.poll()
            stdOut, stdErr = self.process.communicate()

            if self.process.returncode != None:
                self.app.logging.output('tcpdump finished with code: ' + str(self.process.returncode) + ', and output: ' + str(stdOut) + str(stdErr), self.interface)

                if self.process.returncode != 0:
                    self.lastErrorMessage = str(stdOut + stdErr)

                self.thread.terminate()
                return True

            time.sleep(0.5)


    def finish(self):
        """
        Executes on exiting an application or reconfiguring interface
        :return:
        """

        # finish dhcp client process
        if self.find_process('dhclient ' + self.interface):
            self.find_and_kill_process('dhclient ' + self.interface, self.interface)

        # finish tcpdump process
        if self.process:
            self.app.logging.output('Killing tcpdump process pid=' + str(self.process.pid), self.interface)

            try:
                self.process.kill()
            except Exception as e:
                self.app.logging.output('Process tcpdump already killed', self.interface)



    def bring_down_interface(self, interface):
        """
        Bring down the interface

        :param interface:
        :return: bool
        """

        command = ["ifconfig", interface, "down"]

        if self.app.commandsPrefix:
            command = self.app.commandsPrefix + command

        self.app.logging.output('Bringing down interface using ' + str(command))

        status, output = self.app.executeCommand(command)

        if not status:
            self.lastErrorMessage = output

        return status


