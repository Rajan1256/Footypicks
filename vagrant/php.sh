#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive
export LC_ALL=en_US.UTF-8
export LANG=en_US.UTF-8
# -y             Assume that the answer to simple yes/no questions is 'yes'.
# -f             Aggressively try to fix broken packages.
# -q             In command-line mode, suppress the incremental progress indicators.
sudo apt-get -y update
sudo add-apt-repository ppa:ondrej/php
sudo apt-get -y update
sudo apt-get -y upgrate
sudo apt-get install php7.1-fpm
sudo apt-get install -q -y -f php-cli php7.1-curl php-gd php-mbstring php7.1-xml
