# syntax=docker/dockerfile:1.3-labs
FROM debian:trixie
# TODO use PHP image (but then need to build extensions)
LABEL org.opencontainers.image.source https://github.com/hathitrust/catalog

RUN apt-get update && apt-get install -y \
      curl \
      msmtp-mta \
      bsd-mailx \
      php-curl \
      php-fpm \
      php-gd \
      php-http \
      php-ldap \
      php-mbstring \
      php-mysql \
      php-pear \
      php-raphf \
      php-xdebug \
      php-xml \
      php-yaml \
      smarty3

# Actual stuff installed on bullseye for ht-web-preview

RUN curl -O https://phar.phpunit.de/phpunit-9.6.11.phar
RUN chmod +x phpunit-9.6.11.phar && mv phpunit-9.6.11.phar /usr/local/bin/phpunit

RUN pear channel-update pear.php.net && pear install \
      File_MARC \
      HTTP_Request2 \
      Pager

# Default PHP config:
#  -> class { 'php::apache_config':
#
#    settings     => {
#      'PHP/short_open_tag'          => 'On',
#      'PHP/max_input_vars'          => '2000',
#      'PHP/memory_limit'            => '256M',
#      'PHP/error_reporting'         => 'E_ALL & ~E_DEPRECATED',
#      'PHP/upload_max_filesize'     => '32M',
#      'Date/date.timezone'          => 'America/Detroit',
#      'mail function/sendmail_path' => '/usr/sbin/sendmail -t -i'
#    },
#  }

# RUN mkdir /run/php
COPY ./docker/php_pool.conf /etc/php/8.4/fpm/pool.d/www.conf

#https://github.com/docker-library/php/blob/master/7.4/bullseye/fpm/Dockerfile#L266-L271
STOPSIGNAL SIGQUIT

WORKDIR /usr/share/GeoIP
ADD --chmod=644 https://github.com/maxmind/MaxMind-DB/blob/main/test-data/GeoIP2-Country-Test.mmdb?raw=true GeoIP2-Country.mmdb

EXPOSE 9000
WORKDIR /app
CMD ["/app/docker/run_catalog.sh"]
