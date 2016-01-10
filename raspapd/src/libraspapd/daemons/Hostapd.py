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
    thread  = None
    worker  = None
    retry   = 0

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
        self.thread, self.worker = pantheradesktop.tools.createThread(self.start_daemon_thread)

        time.sleep(5)
        return (self.lastErrorMessage == '')




    def start_daemon_thread(self, thread):
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

                # retry when interface could be possibly blocked
                if self.retry == 0 and "Could not configure driver mode" in str(stdErr + stdOut) and self.try_to_free_wireless_interface():
                    self.app.logging.output('Trying again with hostapd', self.interface)
                    self.retry = 1
                    return self.start_daemon_thread(thread)


                if self.process.returncode != 0:
                    self.lastErrorMessage = str(stdOut + stdErr)

                self.thread.terminate()
                return True

            time.sleep(0.5)



    def try_to_free_wireless_interface(self):
        """
        Try to kill all processes that are blocking wireless interface from begin set up as access point
        Uses airmon-ng

        :return:
        """

        self.app.logging.output('Trying to kill processes that uses wireless interfaces using airmon-ng', self.interface)
        stat, out = self.app.executeCommand(['whereis', 'airmon-ng'], logging = False)

        if stat:
            self.app.executeCommand(['airmon-ng', 'check', 'kill'])

        return stat



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
