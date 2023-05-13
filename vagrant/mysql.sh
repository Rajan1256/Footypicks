#!/usr/bin/env bash
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password 123"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password 123"
sudo aptitude install -q -y -f  mysql-server mysql-client php7.1-mysql
mysql -uroot -p123 -e 'CREATE DATABASE `footypicks` CHARACTER SET `utf8` COLLATE `utf8_bin`;'
mysql -uroot -p123 -e 'CREATE DATABASE `footy_test` CHARACTER SET `utf8` COLLATE `utf8_bin`;'