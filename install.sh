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

pwd=`pwd`

# update pf2 as there is a temporary problem on packagist
cd ../raspap/vendor/pantheraframework/panthera
git pull

cd $pwd

# clean up composer
echo "~> Cleaning up composer"
rm composer.phar

# create a system user and give him access
sudo useradd raspap -b $PWD -r -s /bin/false

sudo touch /etc/tor/torrc-raspap
sudo chown raspap:raspap /etc/tor/torrc-raspap

sudo touch /etc/privoxy/config-raspap
sudo chown raspap:raspap /etc/privoxy/config-raspap

sudo chown raspap:raspap $PWD/../raspap -R
sudo chmod 770 $PWD/../raspap

sudo mkdir -p /etc/dhcpd/raspap/
sudo chown raspap:raspap /etc/dhcpd/raspap/ -R
sudo chmod 770 /etc/dhcpd/raspap/

sudo mkdir -p /etc/hostapd/raspap/
sudo chown raspap:raspap /etc/hostapd/raspap/ -R
sudo chmod 770 /etc/hostapd/raspap/

# install raspapd
cd raspapd/
python2 setup.py install

echo "RaspAP installed."
echo "To allow you'r user to overwrite RaspAP files type: "
echo "gpasswd -a your-login-here raspap"