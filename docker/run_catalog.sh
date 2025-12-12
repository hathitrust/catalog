#!/bin/bash

mkdir interface/compile
chmod 777 ./interface/compile

/usr/sbin/php-fpm8.4 -F -O
