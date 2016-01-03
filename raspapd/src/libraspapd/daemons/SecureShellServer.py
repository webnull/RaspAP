from BaseDaemon import BaseDaemon
import time
import os

class SecureShellServer(BaseDaemon):
    """
    Controls Privoxy daemon
    """

    def start(self, interface, settings):
        """
        Start processes

        :param interface:
        :param settings:
        :return:
        """

        # openssh-server
        if 'openssh' in settings:
            if settings['openssh']:
                # copy /etc/ssh/sshd_raspap into /etc/ssh/sshd_config preserving permissions
                if os.path.isfile('/etc/ssh/sshd_raspap'):
                    open('/etc/ssh/sshd_config', 'w').write(open('/etc/ssh/sshd_raspap').read())

                self.callSystemService('sshd', 'restart')
            else:
                self.callSystemService('sshd', 'stop')


        # shell in a box support
        if 'shellinabox' in settings and settings['shellinabox']:
            port = 8021

            # allow custom port
            if 'shellinabox_port' in settings:
                port = int(settings['shellinabox_port'])

            result, output = self.app.executeCommand('cd /tmp && screen -d -m shellinaboxd --port ' + str(port) + ' --no-beep -d', shell=True)

            time.sleep(5)

            if not self.find_process('shellinaboxd', withoutSudo=False):
                self.app.logging.output('shellinaboxd process unexpectedly quit', interface)
                return False

            return True


    def finish(self):
        """
        Shutdown processes
        :return:
        """

        self.callSystemService('sshd', 'stop')
        return self.find_and_kill_process('shellinaboxd', self.interface)