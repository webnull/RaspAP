#-*- encoding: utf-8 -*-
import time
import datetime
import daemons
import json
import traceback
import sys

class databaseWatcher:
    """
        RaspAP
        --
        Database Watcher, watching "interfaces" table for changes
    """

    """ :var application app """
    app = None

    """ List of all interfaces and it's last update, if date from database is newer then it means that we have to perform a task """
    time_table = {

    }

    """ Here are stored all daemons per interface from previous iteration """
    interface_daemons = {

    }

    def __init__(self, app):
        self.app = app

        self.app.hooking.addOption('app.pa_exit', self.close_all_interfaces)


    def compareChanges(self, interface, date):
        """
        Compare changes to the interface

        :param interface:
        :param date:
        :return: bool
        """

        try:
            datetimestamp = int(datetime.datetime.strptime(date, '%Y-%m-%d %H:%M:%S').strftime("%s"))
        except Exception as e:
            self.app.logging.output('Cannot compare date - ' + str(date), interface)
            datetimestamp = 1

        if not interface in self.time_table:
            self.time_table[interface] = 0

        return (self.time_table[interface] < datetimestamp)


    def close_all_interfaces(self, data = ''):
        """
        Close all interfaces

        :param data: Used in a hook
        :return:
        """

        self.app.logging.output('Finishing all tasks...')

        for interface, daemons in self.interface_daemons.iteritems():
            self.finish_previous_tasks(interface)


    def finish_previous_tasks(self, interface):
        """
        Finish tasks from previous iteration in case of update and restarting of all services

        :param interface:
        :return:
        """

        if not interface in self.interface_daemons:
            return False

        for daemon in self.interface_daemons[interface]:
            self.app.logging.output('Sending finish signal to ' + interface + '/' + daemon, interface)
            self.interface_daemons[interface][daemon].finish()


    def executeTask(self, interface, daemonslist):
        """
        Execute update task for interface

        :param interface:
        :param daemonslist:
        :return:
        """

        self.app.logging.output('Received task request: ' + str(daemonslist) + ' for interface ' + interface, interface)

        if not interface in self.interface_daemons:
            self.interface_daemons[interface] = {}
        else:
            self.finish_previous_tasks(interface)


        daemonslist = json.loads(daemonslist)
        failed = False

        for daemon, settings in daemonslist.iteritems():
            errorMessage = ''
            daemon = daemon[0].upper() + daemon[1:]


            if not self.daemonExists(daemon):
                self.app.logging.output('Warning! Daemon "' + str(daemon) + '" does not exists!', interface)
                continue

            try:
                #: BaseDaemon: Daemon object
                daemonObj = eval('daemons.' + daemon + '.' + daemon + '(self.app)')
                self.interface_daemons[interface][daemon] = daemonObj

                result = daemonObj.start(interface, settings)
                errorMessage = daemonObj.getErrorMessage()

            except Exception as e:
                result = None
                traceback.print_exc(file=sys.stdout)
                errorMessage = str(e)


            if not result:
                self.app.logging.output('Task "' + str(daemon) + '" for interface "' + str(interface) + '" not completed successfuly')
                self.app.db.query('UPDATE interfaces SET fail_message = :fail_message WHERE name = :name', {
                    'fail_message': errorMessage,
                    'name': interface
                })
                failed = True
                break

        if not failed:
            # reset last error message
            self.app.db.query('UPDATE interfaces SET fail_message = :fail_message WHERE name = :name', {
                'fail_message': '',
                'name': interface
            })

        return not failed


    def daemonExists(self, daemon):
        """
        Checks if daemon exists

        :param str daemon: Daemon name eg. "NetworkStack"
        :return: bool
        """

        return hasattr(daemons, daemon)


    def updateTimestamp(self, interface, date):
        """
        Mark interface as updated for timestamp from database

        :param interface:
        :param date:
        :return:
        """

        self.time_table[interface] = int(datetime.datetime.strptime(date, '%Y-%m-%d %H:%M:%S').strftime("%s"))


    def performCheck(self):
        """
        Check if database was updated
        :return:
        """

        results = self.app.db.query('SELECT * FROM interfaces').fetchAll()

        for interface in results:
            if self.compareChanges(interface['name'], interface['last_updated']):
                self.executeTask(interface['name'], interface['daemons'])
                self.updateTimestamp(interface['name'], interface['last_updated'])



    def watchLoop(self, thread):
        timeout = 5

        while True:
            self.performCheck()
            time.sleep(timeout)