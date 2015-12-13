#-*- encoding: utf-8 -*-

import time
import os
import sys
import subprocess

import pantheradesktop.kernel
import pantheradesktop.tools
import pantheradesktop.db

from libraspapd.databaseWatcher import databaseWatcher


class application (pantheradesktop.kernel.pantheraDesktopApplication, pantheradesktop.kernel.Singleton):
    """
        RaspAP Daemon
        --
        Main Class
    """

    """ :var databaseWatcher databaseWatcher """
    databaseWatcher = None

    """ :var str commandsPrefix Prefix applied to every executed command """
    commandsPrefix = ['sudo', '-n']

    def initializeDatabase(self):
        """
        Configure and initialize the database

        :return:
        """

        # we are using only SQLite3
        self.config.setKey('databaseType', 'sqlite3')

        # detect if launched from "daemon" directory
        if os.path.isfile('../.content/database.sqlite3'):
            self.config.setKey('databaseFile', os.path.abspath('../.content/database.sqlite3'))


        if not os.path.isfile(self.config.getKey('databaseFile')):
            print("Cannot find RaspAP Web Panel database file, please configure \"databaseFile\" configuration entry with a correct path")
            sys.exit(1)

        self.db = pantheradesktop.db.pantheraDB(self)


    def mainLoop(self, arg = ''):
        """
        Main loop

        :param arg:
        :return:
        """

        self.initializeDatabase()

        self.databaseWatcher = databaseWatcher(self)
        thread, worker = pantheradesktop.tools.createThread(self.databaseWatcher.watchLoop)

        while True:
            try:
                time.sleep(10)
            except KeyboardInterrupt:
                break

        thread.terminate()

    def executeCommand(self, command, shell = False):
        """
        Executes a shell command

        :param command:
        :param shell: Execute in emulated shell (less secure)
        :return:
        """

        if shell:
            if isinstance(command, list):
                command = ' '.join(command)

            self.logging.output(command)
            pipes = subprocess.Popen(command, shell=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        else:
            self.logging.output(str(' '.join(command)))
            pipes = subprocess.Popen(command, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

        stdOut, stdErr = pipes.communicate()

        stdOut = stdOut.decode('utf-8')
        stdErr = stdErr.decode('utf-8')

        if pipes.returncode != 0:
            self.logging.output('Process returned code ' + str(pipes.returncode) + ', and message: "' + stdOut + stdErr + '"')

        return (pipes.returncode == 0), stdOut.strip() + stdErr.strip()