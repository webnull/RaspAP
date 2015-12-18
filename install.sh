#!/bin/bash

# installing all dependencies
# Arch Linux support
if [ -e /usr/bin/pacman ];
then
    sudo pacman -S php php-sqlite python2 python2-pip dhclient dhcp tor privoxy hostapd
    sudo pecl install pam

# Debian/Ubuntu/Mint support
elif [ -e /usr/bin/apt ];
then
    sudo apt install php php5-sqlite python2.7 isc-dhcp-server tor privoxy hostapd
fi

cd raspap

# install composer
echo "~> Installing composer"
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install

# clean up composer
echo "~> Cleaning up composer"
rm composer.phar

# create a system user and give him access
useradd raspap -b $PWD -r -s /bin/false
chown raspap $PWD/raspap -R

mkdir -p /etc/dhcpd/raspap/
chown raspap /etc/dhcpd/raspap/ -R

mkdir -p /etc/hostapd/raspap/
chown raspap /etc/hostapd/raspap/ -R