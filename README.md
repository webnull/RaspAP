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
- panthera-desktop (using pip - pip2 install pantheradesktop)
- raintpl4 (will install automaticaly with composer)
- isc-dhcp (install using OS package manager)
- dhclient (install using OS package manager)
- python-pam (install via PIP for Python 2.7 - `sudo pip2 install python-pam`)
- GNU Screen (install via OS package manager: screen)
- wireless-tools (install using OS package manager)
- bridge-utils (install using OS package manager)
- iw (install using OS package manager)
- iptables (install using OS package manager)
- wpa_supplicant (install using OS package manager)

## Optional dependencies
- tor
- proxivy

## FAQ

1. How to authorize my login to use web panel?

Add your login (same as to operating system) to /etc/RaspAP/RaspAP.conf in "SudoUsers" array.

Example:
```json
{
    "SudoUsers": [ "root", "my-login", "damien", "ann", "zdzichu-dobry-admin" ]
}
```

2. If DHCPD does not work on Ubuntu
Try to disable apparmor profile for isc-dhcpd-server:

```bash
ln -s /etc/apparmor.d/usr.sbin.dhcpd /etc/apparmor.d/disable/
apparmor_parser -R  /etc/apparmor.d/usr.sbin.dhcpd
```

3. Hostapd returns that it cannot change interface mode

```bash
nl80211: Could not configure driver mode
```

Please install `airmon-ng` (from `aircrack-ng` package) and use it to list all processes that are **blocking your network interface** and kill those processes at first.

```bash
airmon-ng check kill
```

Also `raspapd` daemon should detect installed `airmon-ng` and free interface automatically.

## Installation from sources
At first please install panthera-desktop from here: https://github.com/Panthera-Framework/Panthera-Desktop

Please run ./install.sh to install from sources,
and to run Python daemon please run run-daemon.sh

Next please add following lines to /etc/sudoers:

```bash
raspap ALL=(root) NOPASSWD: /usr/bin/raspapd-pam.py
raspap ALL=(root) NOPASSWD: /usr/local/bin/raspapd-pam.py
raspap ALL=(root) NOPASSWD: /srv/http/raspap-webgui/raspapd/raspapd-pam.py
```

In last line change only `/srv/http/raspap-webgui/raspapd/raspapd-pam.py` to real path to file `/raspapd/raspapd-pam.py` from the project in your filesystem.