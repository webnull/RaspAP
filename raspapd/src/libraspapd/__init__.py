#-*- encoding: utf-8 -*-

from libraspapd.application import application
from libraspapd.args import arguments

def runInstance():
    """
        RaspAP
        --
        Run instance of application
    """

    kernel = application()
    kernel.appName = 'raspapd'
    kernel.coreClasses['gui'] = False
    kernel.coreClasses['db'] = False
    kernel.coreClasses['argsparsing'] = arguments
    kernel.initialize(quiet=True)
    kernel.hooking.addOption('app.mainloop', kernel.mainLoop)
    kernel.main()