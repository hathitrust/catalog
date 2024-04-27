#!/bin/bash

mkdir -p interface/compile
chmod 777 ./interface/compile

/usr/sbin/php-fpm8.2 -F -O
