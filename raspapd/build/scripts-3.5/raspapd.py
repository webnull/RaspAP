#!/usr/bin/python
#-*- encoding: utf-8 -*-
import sys
import os

__author__ = "Damian Kęska"
__license__ = "LGPLv3"
__maintainer__ = "Damian Kęska"
__copyright__ = "Copyleft by Damian Kęska"

# get current working directory to include local files (developer mode)
t = sys.argv[0].replace(os.path.basename(sys.argv[0]), "") + "src/"

if os.path.isdir(t):
    sys.path.append(t)

import libraspapd
libraspapd.runInstance()