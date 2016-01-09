#!/bin/bash

if [ "$1" == "--help" ] || [ "$1" == "-h" ]
then
    echo "install.sh [--developer-deploy, -d] [--help, -h]"
    exit
fi

cd raspap

if [ "$1" != "--developer-deploy" ] && [ "$1" != "-d" ]
then
    # installing all dependencies
    # Arch Linux support
    if [ -f /usr/bin/pacman ];
    then
        sudo pacman -S php php-sqlite python2 aircrack-ng extra/python2-pyqt4 python2-pip dhclient dhcp tor privoxy hostapd screen

    # Debian/Ubuntu/Mint support
    elif [ -f /usr/bin/apt ];
    then
        sudo apt install php5-cli php5-cgi php5-sqlite python2.7 aircrack-ng python-qt4 python-pip isc-dhcp-server tor privoxy hostapd screen
    fi

    # install python dependenices
    sudo pip2 install python-pam
    sudo pip2 install pantheradesktop

    # install composer
    echo "~> Installing composer"
    php -r "readfile('https://getcomposer.org/installer');" | php
    php composer.phar install

    # clean up composer
    echo "~> Cleaning up composer"
    rm composer.phar

    # create a system user and give him access
    sudo useradd raspap -b $PWD -r -s /bin/false

    sudo touch /etc/tor/torrc-raspap
    sudo chown raspap:raspap /etc/tor/torrc-raspap

    sudo touch /etc/ssh/sshd_raspap
    sudo chmod 770 /etc/ssh/sshd_raspap
    sudo chown raspap:raspap /etc/ssh/sshd_raspap

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
fi

# install raspapd
cd ../raspapd/
python2 setup.py install

if [ ! -f /usr/share/webapps ]
then
    mkdir -p /usr/share/webapps
fi

if [ -f /usr/share/webapps/raspap ]
then
    rm -rf /usr/share/webapps/raspap
fi

cd ../
cp ./ /usr/share/webapps/raspap -pr

# deploy database in a destination installation
cd /usr/share/webapps/raspap/raspap
sudo -u raspap ./vendor/pantheraframework/panthera/lib/Binaries/deploy Build/Database/ConfigurePhinx
sudo -u raspap ./vendor/pantheraframework/panthera/lib/Binaries/deploy Build/Database/Migrate
sudo -u raspap ./vendor/pantheraframework/panthera/lib/Binaries/deploy Build/Routing/Cache

echo "RaspAP installed in /usr/share/webapps/raspap"
echo "To allow you'r user to overwrite RaspAP files type: "
echo "gpasswd -a your-login-here raspap"

if [ ! -f /etc/RaspAP/RaspAP.conf ]
then
    mkdir /etc/RaspAP
    read -p "Would you like to add user $SUDO_USER to list of allowed to login to RaspAP web panel?" yn
    case $yn in
        [Yy]* ) echo "{\"SudoUsers\": [\"$SUDO_USER\", \"root\"]}" > /etc/RaspAP/RaspAP.conf; break;;
        [Nn]* ) exit;;
        * ) echo "Please answer yes or no.";;
    esac
fi

