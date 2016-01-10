#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd "$DIR/raspap"
sudo -u raspap ./vendor/pantheraframework/panthera/lib/Binaries/webserver --server PHP start
read