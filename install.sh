#!/bin/bash
if [ `whoami` != "root" ]
then
    eval "sudo ${BASH_SOURCE} $@"
    exit
fi

_help=0
_developerDeploy=0
_erasePreviousDatabase=0
_runAfterDeploy=0
_keepCache=0

for var in "$@"
do
    if [ ${var} == "--help" ] || [ ${var} == "-h" ]; then _help=1; fi;
    if [ ${var} == "--developer-deploy" ] || [ ${var} == "-d" ]; then _developerDeploy=1; fi;
    if [ ${var} == "--erase-previous-database" ] || [ ${var} == "-e" ]; then _erasePreviousDatabase=1; fi;
    if [ ${var} == "--run" ] || [ ${var} == "-r" ]; then _runAfterDeploy=1; fi;
    if [ ${var} == "--keep-cache" ] || [ ${var} == "-c" ]; then _keepCache=1; fi;
done

if [ ${_help} == 1 ]
then
    echo "install.sh [--developer-deploy, -d] [--help, -h] [--erase-previous-database -e] [--run -r] [--keep-cache -c]"
    exit
fi

cd raspap

if [ ${_developerDeploy} == 0 ]
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

if [ -d /usr/share/webapps/raspap ]
then
    if [ ${_erasePreviousDatabase} == "0" ]
    then
        echo "Creating backup of previous database"
        cp /usr/share/webapps/raspap/raspap/.content/database.sqlite3 /tmp/database.backup.sqlite3
    fi

    if [ ${_keepCache} == "1" ]
    then
        echo "Creating backup of cache"

        if [ -d /tmp/.raspap-cache ]
        then
            rm -rf /tmp/.raspap-cache
        fi

        cp /usr/share/webapps/raspap/raspap/.content/cache /tmp/.raspap-cache -pR
    fi

    echo "Removing previous instalation"
    rm -rf /usr/share/webapps/raspap
fi

echo "Copying files..."
cd ../
cp ./ /usr/share/webapps/raspap -pr

if [ ${_keepCache} == "1" ] && [ -d /tmp/.raspap-cache ]
then
    echo "Restoring cache from backup"
    rm -rf /usr/share/webapps/raspap/raspap/.content/cache
    cp /tmp/.raspap-cache /usr/share/webapps/raspap/raspap/.content/cache -pR
fi

# restoring database from backup
if [ ${_erasePreviousDatabase} == 0 ] && [ -f /tmp/database.backup.sqlite3 ]
then
    echo "Restoring database"
    cp /tmp/database.backup.sqlite3 /usr/share/webapps/raspap/raspap/.content/database.sqlite3
fi

# deploy database in a destination installation
cd /usr/share/webapps/raspap/raspap
sudo -u raspap ./vendor/pantheraframework/panthera/lib/Binaries/deploy Build/Database/ConfigurePhinx
sudo -u raspap ./vendor/pantheraframework/panthera/lib/Binaries/deploy Build/Database/Migrate
sudo -u raspap ./vendor/pantheraframework/panthera/lib/Binaries/deploy Build/Routing/Cache

echo "Setting permisions..."
if [ ! -d /usr/share/webapps/raspap/raspap/.content/cache/sessions ]
then
    mkdir /usr/share/webapps/raspap/raspap/.content/cache/sessions -p
fi
chown raspap:raspap /usr/share/webapps/raspap/raspap/.content/cache/sessions -R

sudo chown raspap:raspap /usr/share/webapps/raspap/raspap/.content/cache/ -R
sudo chmod 770 /usr/share/webapps/raspap/raspap/.content/cache/ -R

sudo chown raspap:raspap /usr/share/webapps/raspap/raspap/.content/*.sqlite3 -R
sudo chmod 770 /usr/share/webapps/raspap/raspap/.content/*.sqlite3 -R

echo "RaspAP installed in /usr/share/webapps/raspap"
echo "To allow you'r user to overwrite RaspAP files type: "
echo "gpasswd -a your-login-here raspap"

echo ""
echo "To run application daemon and webpanel please do:"
echo "sudo /usr/share/webapps/raspap/run-webpanel.sh"
echo "sudo /usr/share/webapps/raspap/run-daemon.sh"
echo ""

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

if [ ${_runAfterDeploy} == "1" ]
then
    killall run-webpanel.sh
    killall run-daemon.sh

    screen -dmS raspap /usr/share/webapps/raspap/run-webpanel.sh
    screen -dmS raspapd /usr/share/webapps/raspap/run-daemon.sh

    echo ""
    echo "Raspap and Raspapd started, type \"screen -rD raspap\" or \"screen -rD raspapd\" to invoke sessions"
    echo "Use ctrl+a+d to hide them to background again"
    echo ""
fi