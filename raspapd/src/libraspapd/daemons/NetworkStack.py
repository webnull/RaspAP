#-*- encoding: utf-8 -*-
import subprocess
import socket
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

    # dhclient monitoring
    dhThread = None
    dhWorker = None
    connectivityCheckEnabled  = True
    connectivityCheckInterval = 45
    connectivityCheckIP       = '8.8.8.8'
    timeout  = 60

    def start(self, interface, settings):
        """
        Constructor

        :param str interface:
        :param Dict settings:
        :return: networkStack
        """

        self.interface = interface

        if "client_dhcp" in settings:
            return self.run_dhcp_client(settings)

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

        if 'gateway' in settings and settings['gateway']:
            commands.append('ifconfig ' + interface + ' gateway ' + settings['gateway'])

        commands.append('ifconfig ' + interface + ' ' + settings['address'])

        if 'broadcast' in settings and settings['broadcast']:
            commands.append('ifconfig ' + interface + ' broadcast ' + settings['broadcast'])

        if 'netmask' in settings and settings['netmask']:
            commands.append('ifconfig ' + interface + ' netmask ' + settings['netmask'])

        commands.append('ifconfig ' + interface + ' up')

        for command in commands:
            status, message = self.app.executeCommand(command, shell=True)

            if not status:
                self.lastErrorMessage = message
                return False

        return True


    def run_dhcp_client(self, settings):
        """
        Launch DHCP client and monitor interface for internet connection
        Returns status of first try

        :param interface:
        :param settings:
        :return:
        """

        # set ip for monitoring, defaults to disabled connectivity check
        if 'monitor_ip' in settings:
            try:
                socket.inet_aton(settings['monitor_ip'])
                self.connectivityCheckIP = settings['monitor_ip']
            except socket.error:
                self.connectivityCheckEnabled = False


        # set monitoring interval (defaults to 45 seconds)
        if 'monitor_interval' in settings:
            try:
                self.connectivityCheckInterval = int(settings['monitor_interval'])
            except Exception:
                pass


        # allow to set custom dhcp timeout (defaults to 60 seconds)
        if 'dhcp_timeout' in settings:
            try:
                self.timeout = settings['dhcp_timeout']
            except ValueError:
                pass


        self.dhThread, self.dhWorker = pantheradesktop.tools.createThread(self.monitor_dhcp_connection)
        return self.dhcp_client()



    def monitor_dhcp_connection(self, thread = ''):
        """
        Monitor dhcp connection for given ip address, if it will fail, then try to reconnect

        :param thread:
        :return:
        """

        while True:
            time.sleep(self.connectivityCheckInterval)
            self.app.logging.output('Checking connectivity for interface ' + self.interface, self.interface)
            status, output = self.app.executeCommand([ 'ping', '-c', '1', self.connectivityCheckIP, '-W', '5' ], logging = False)

            if not status:
                self.app.logging.output('Restarting DHCP client on interface ' + self.interface, self.interface)
                self.dhcp_client()



    def dhcp_client(self):
        """
        Run "dhclient" on interface (ISC DHCP Client)
        :param interface:
        :return:
        """

        command = [ 'dhclient', self.interface, '-e', 'timeout=' + str(self.timeout) ]

        # check for commands availability
        status, output = self.app.executeCommand([ 'whereis', 'dhclient' ])

        if not status:
            command = [ 'dhcpcd', self.interface, '-t', str(self.timeout) ]

        status, output = self.app.executeCommand(command)

        if not status:
            if not output:
                output = str(command) + ' process finished with wrong code, no ip address assigned'

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
        self.thread, self.worker = pantheradesktop.tools.createThread(self.run_tcpdump)

        time.sleep(5) # wait for tcpdump to start, 5 seconds should be enough even on slower machines
        return (self.lastErrorMessage == '')


    def run_tcpdump(self, thread):
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

        command = [ "ifconfig", interface, "down" ]

        if self.app.commandsPrefix:
            command = self.app.commandsPrefix + command

        self.app.logging.output('Bringing down interface using ' + str(command))

        status, output = self.app.executeCommand(command)

        if not status:
            self.lastErrorMessage = output

        return status


