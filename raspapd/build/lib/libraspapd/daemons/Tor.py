from BaseDaemon import BaseDaemon
import time

class Tor(BaseDaemon):
    """
    Controls TOR daemon
    """

    def start(self, interface, settings):
        """
        Starts TOR

        :param interface:
        :param settings:
        :return:
        """

        # by default RaspAP should configure tor to run as daemon (RunAsDaemon 1)
        result, output = self.app.executeCommand('tor -f /etc/tor/torrc-raspap', shell=True)

        if not self.find_process('tor -f /etc/tor/torrc-raspap'):
            time.sleep(1)
            self.app.logging.output('TOR process unexpectedly quit', interface)
            return False

        return True


    def finish(self):
        """
        Shutdown TOR
        :return:
        """

        return self.find_and_kill_process('tor -f /etc/tor/torrc-raspap', self.interface)