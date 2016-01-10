#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd "$DIR/raspapd"
sudo python2.7 ./raspapd.py --debug
read