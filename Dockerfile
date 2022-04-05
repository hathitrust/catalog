# syntax=docker/dockerfile:1.3-labs
FROM debian:bullseye
# TODO use PHP image (but then need to build extensions)
LABEL org.opencontainers.image.source https://github.com/hathitrust/catalog

RUN apt-get update && apt-get install -y \
      msmtp-mta \
      bsd-mailx \
      php-curl \
      php-fpm \
      php-gd \
      php-geoip \
      php-http \
      php-ldap \
      php-mysql \
      php-mdb2 \
      php-mdb2-driver-mysql \
      php-xsl \
      php-mbstring \
      pear-channels \
      php-yaml

# Actual stuff installed on bullseye for ht-web-preview

# PHPUnit               1.3.2   stable -- MISSING


RUN pear channel-update pear.php.net && pear install \
      Auth_SASL \
      DB \
      DB_DataObject \
      File_CSV \
      File_MARC \
      HTTP_Request \
      HTTP_Request2 \
      HTTP_Session2-beta \
      Log \
      Mail \
      Net_SMTP \
      Net_URL_Mapper-beta \
      Pager \
      PhpDocumentor \
      PHP_Compat \
      Structures_DataGrid-beta \
      Structures_LinkedList-beta \
      XML_Parser \
      XML_Beautifier \
      XML_Serializer-beta

RUN apt-get install -y wget && wget -q -O /usr/share/keyrings/hathitrust-archive-keyring.gpg https://hathitrust.github.io/debian/hathitrust-archive-keyring.gpg && \
    echo "deb [signed-by=/usr/share/keyrings/hathitrust-archive-keyring.gpg] https://hathitrust.github.io/debian/ bullseye main" > /etc/apt/sources.list.d/hathitrust.list

RUN apt-get update && apt-get install php-smarty

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

RUN mkdir /run/php
COPY ./docker/php_pool.conf /etc/php/7.4/fpm/pool.d/www.conf

#https://github.com/docker-library/php/blob/master/7.4/bullseye/fpm/Dockerfile#L266-L271
STOPSIGNAL SIGQUIT

EXPOSE 9000
CMD ["/usr/sbin/php-fpm7.4","-F","-O"]
