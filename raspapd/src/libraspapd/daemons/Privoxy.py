from BaseDaemon import BaseDaemon
import time

class Privoxy(BaseDaemon):
    """
    Controls Privoxy daemon
    """

    def start(self, interface, settings):
        """
        Start privoxy in daemon mode

        :param interface:
        :param settings:
        :return:
        """

        result, output = self.app.executeCommand('privoxy /etc/privoxy/config-raspap', shell=True)

        time.sleep(1)

        if not self.find_process('privoxy /etc/privoxy/config-raspap'):
            self.app.logging.output('Privoxy process unexpectedly quit', interface)
            return False

        return True


    def finish(self):
        """
        Shutdown the privoxy
        :return:
        """

        return self.find_and_kill_process('privoxy /etc/privoxy/config-raspap', self.interface)