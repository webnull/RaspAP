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

        if "downInterface" in settings:

            if not settings['useInterface']:
                self.app.logging.output('Skipping interface ' + interface, interface)
                return True

            return self.bringDownInterface(interface)

        if "monitor_filter" in settings:
            return self.configureMonitoring(interface, settings)


    def configureMonitoring(self, interface, settings):
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

        if self.process:
            self.app.logging.output('Killing tcpdump process pid=' + str(self.process.pid), self.interface)

            try:
                self.process.kill()
            except Exception as e:
                self.app.logging.output('Process tcpdump already killed', self.interface)



    def bringDownInterface(self, interface):
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


