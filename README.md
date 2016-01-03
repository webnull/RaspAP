![RaspAP](http://i.imgur.com/xeKD93p.png)
# `$ RaspAP`

This project allows you to create a home-made router from any computer, it does not matter if it's a laptop, or a Raspberry Pi, or a server.
Written in PHP and Python allows to be used on various hardware, works on x86, arm and all architectures that are supported by Linux kernel
and where runs dependencies.

## Features
- Creating a wireless hotspot, or multiple hotspots on wireless interfaces (USB cards working!)
- Support for WPA 2 Enterprise
- Connecting to a wired network using dhcp or static address
- Setting up a monitoring mode on wireless interface and sniffing network using tcpdump
- Setting up a TOR proxy, relay, exit node and bridge
- Logging in using Linux system account (PAM support)
- Bridge interfaces (cable connected clients could connect to wireless hotspot users and vice-versa)

## Not done yet
- Mac ACL
- WPS
- WPA2 Enterprise valid PSK

## Dependencies

- python2.7 (install using OS package manager)
- Linux
- PHP >=5.6 (install using OS package manager)
- php-sqlite (install using OS package manager)
- pantheraframework2 (will install automaticaly with composer)
- panthera-desktop (https://github.com/Panthera-Framework/Panthera-Desktop)
- raintpl4 (will install automaticaly with composer)
- isc-dhcp (install using OS package manager)
- dhclient (install using OS package manager)
- python-pam (install via PIP for Python 2.7 - `sudo pip2 install python-pam`)

## Optional dependencies
- tor
- proxivy

## FAQ

1. How to authorize my login to use web panel?

Navigate to .content/, and edit app.php file, nano .content/app.php
Append:

```php
'SudoUsers' => [ 'your-login-here', 'root', 'your-other-login-here' ]
```

## Installation from sources
At first please install panthera-desktop from here: https://github.com/Panthera-Framework/Panthera-Desktop

Please run ./install.sh to install from sources,
and to run Python daemon please run run-daemon.sh

Next please add following lines to /etc/sudoers:

```bash
raspap ALL=(root) NOPASSWD: /usr/bin/raspapd-pam
raspap ALL=(root) NOPASSWD: /srv/http/raspap-webgui/raspapd/raspapd-pam.py
```

In last line change only `/srv/http/raspap-webgui/raspapd/raspapd-pam.py` to real path to file `/raspapd/raspapd-pam.py` from the project in your filesystem.