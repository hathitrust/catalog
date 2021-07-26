#!/bin/bash -l

DIR=$1

if [ -z $DIR ]; then
  DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
fi

perl $DIR/gfv.pl language > $DIR/language.txt
perl $DIR/gfv.pl format > $DIR/format.txt
php -f  $DIR/getHTCollectionMap.php
