#-*- encoding: utf-8 -*-
import subprocess
import time
import pantheradesktop.tools

from BaseDaemon import BaseDaemon

class Hostapd(BaseDaemon):
    """
        RaspAP
        --
        Starts/stops hostapd daemon per interface
    """

    process = None
    thread = None
    worker = None

    def start(self, interface, settings):
        """
        Starts hostapd daemon

        :param interface:
        :param settings:
        :return:
        """

        try:
            if int(open('/proc/sys/kernel/random/entropy_avail').read()) < 1000:
                self.app.logging.output('Warning: Low kernel entropy, wireless could be working slowly. Solution: You could install and enable "haveged"')
        except Exception:
            pass

        self.interface = interface
        self.thread, self.worker = pantheradesktop.tools.createThread(self.startDaemonThread)

        time.sleep(5)
        return (self.lastErrorMessage == '')


    def startDaemonThread(self, thread):
        """
        Starts daemon in a thread
        :return:
        """

        options = [
            'hostapd', '/etc/hostapd/raspap/' + self.interface + '.conf'
        ]

        if self.app.commandsPrefix:
            options = self.app.commandsPrefix + options

        self.app.logging.output('Executing ' + str(' '.join(options)), self.interface)
        self.process = subprocess.Popen(options, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

        while True:
            self.process.poll()
            stdOut, stdErr = self.process.communicate()

            if self.process.returncode != None:
                self.app.logging.output('hostapd finished with code: ' + str(self.process.returncode) + ', and output: ' + str(stdOut) + str(stdErr), self.interface)

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
            self.app.logging.output('Killing hostapd process pid=' + str(self.process.pid), self.interface)

            try:
                self.process.terminate()
            except Exception as e:
                self.app.logging.output('Process hostapd already killed', self.interface)

            return self.find_and_kill_process('hostapd /etc/hostapd/raspap/' + self.interface, self.interface)
