#!/bin/bash

# create missing php log directory
if [ ! -d "/home/freestuff/storage/logs" ]; then
  mkdir -p /home/freestuff/storage/logs
fi

# Start PHP-FPM in the background
php-fpm8.3 -D

# Start Apache in the foreground
apache2ctl -D FOREGROUND