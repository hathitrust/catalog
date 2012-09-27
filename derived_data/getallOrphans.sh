#!/bin/bash


DIR=$1

#RIGHTS=orphcand
RIGHTS=opb


perl $DIR/gfv.pl language 0 ht_rightscode:$RIGHTS > $DIR/orphans/language.txt
perl $DIR/gfv.pl format 0 ht_rightscode:$RIGHTS > $DIR/orphans/format.txt
#php -f  $DIR/getHTNamespaceMap.php > $DIR/orphans/ht_namespaces.php
