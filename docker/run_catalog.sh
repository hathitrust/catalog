#!/bin/bash

mkdir interface/compile
chmod 777 ./interface/compile

/usr/sbin/php-fpm7.4 -F -O
