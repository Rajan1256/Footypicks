#!/usr/bin/env bash
sudo curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer config -g github-oauth.github.com a89b572c8d006a10b323ca398d0fee6a784ca2d8
sudo aptitude install -q -y -f git
composer install
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version