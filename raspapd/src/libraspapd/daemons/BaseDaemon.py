#-*- encoding: utf-8 -*-
import time
import re

class BaseDaemon:
    app = None
    lastErrorMessage = ''
    interface = ''

    def __init__(self, app):
        """
        Constructor

        :param Application app: Application's main class instance, kernel
        :return:
        """
        self.app = app

    def start(self, interface, settings):
        """
        Start process for interface

        :param interface:
        :param settings:
        :return:
        """

    def getErrorMessage(self):
        """
        Get last error message if any

        :return: str
        """

        return self.lastErrorMessage

    def finish(self):
        """
        Executes on exiting an application or reconfiguring interface

        When new configuration is specified at first we have to kill previously ran daemons
        and then apply new configuration

        :return:
        """

        raise Exception('Implement me: finish()')


    def callSystemService(self, service, action = 'restart'):
        """
        Calls systemd, service or openrc service manager

        :param service: Service name
        :param str action: start|stop|restart
        :return:
        """

        if "systemctl: /" in subprocess.check_output('whereis systemctl', shell=True):
            command = ['systemctl', action, service]
        elif "service: /" in subprocess.check_output('whereis service', shell=True):
            command = ['service', service, action]
        elif os.path.isfile('/etc/init.d/' + service):
            command = ['/etc/init.d/' + service, action]
        else:
            command = ['/etc/rc.d/' + service, action]

        return self.app.executeCommand(command)

    def find_and_kill_process(self, find_by, interface, withoutSudo = True):
        """
        Find process using "ps" and terminate

        :param str find_by: text to find process by
        :param str interface: interface name

        :return:
        """

        search = self.find_process(find_by)

        if not search:
            self.app.logging.output('Cannot find pid for "' + find_by + '" for interface "' + interface + '"', interface)
            return False
        else:
            self.app.logging.output('Killing "' + find_by + ' of pid=' + str(search), interface)
            self.app.executeCommand(['kill', search])
            time.sleep(1)
            self.app.executeCommand(['kill', '-9', search])
            return True


    def find_process(self, find_by, withoutSudo = True):
        """
        Find process using "ps"

        :param find_by:
        :return:
        """

        wSudo = ''

        if withoutSudo:
            wSudo = '| grep -v "sudo"'

        result, output = self.app.executeCommand([
            'ps x | grep "' + find_by + '" ' + wSudo + ' | grep -v grep'
        ], shell=True)
        search = re.findall('([0-9]+)(\d+)', output)

        if search:
            return ''.join(search[0])

        return ''