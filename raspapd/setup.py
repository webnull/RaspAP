#!/usr/bin/env python
#-*- encoding: utf-8 -*-

from distutils.core import setup

setup(name='raspapd',
      description = "RaspAP Daemon, router from your Linux box",
      long_description = "Creates a router from Linux based computer",
      author = "Damian Kęska",
      author_email = "webnull.www@gmail.com",
      version="0.1",
      license = "LGPL",
      package_dir={'': 'src'},      
      packages=['libraspapd', 'libraspapd.daemons'],
      data_files = [],
      scripts = ['raspapd.py', 'raspapd-pam.py']
     )
