#!/bin/bash -l

DIR=$1

perl $DIR/gfv.pl language > $DIR/language.txt
perl $DIR/gfv.pl format > $DIR/format.txt
php -f  $DIR/getHTNamespaceMap.php
