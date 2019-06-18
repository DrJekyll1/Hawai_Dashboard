#!/bin/bash

### Non-root user
adduser --disabled-password --gecos '' me

### PHP-Composer
echo -e "\nRunning PHP-Composer..."
su me -c 'php /usr/local/bin/composer install'
su me -c 'php /usr/local/bin/composer update'

### Wait for the database service
echo -e "\nWaiting for the Database-Service..."
dockerize -wait tcp://database:3306 -timeout 60s

### Apache starten
source /etc/apache2/envvars
tail -F /var/log/apache2/* &
sh /start_safe_perms.sh
echo -e "\nApache2 is starting..."
sh /usr/sbin/apache2ctl -D FOREGROUND
