#!/bin/bash

HOST=`hostname | perl -ne '@h = split(/[-.]/); print $h[0]'`

if [ $HOST == 'koolaid' ]
then
  DIR=/htapps/dueberb.catalog/web/derived_data
else
  DIR=/htapps/catalog/web/derived_data
fi


perl $DIR/gfv.pl language > $DIR/language.txt
perl $DIR/gfv.pl format > $DIR/format.txt
php -f  $DIR/getHTNamespaceMap.php > $DIR/ht_namespaces.php
