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

## Not done yet
- Bridge interfaces
- Mac ACL
- WPS

## Dependencies

- python2.7
- Linux
- PHP >=5.6
- php-sqlite
- pantheraframework2
- panthera-desktop
- raintpl4
- isc-dhcp
- dhclient
- pam (pecl extension)

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