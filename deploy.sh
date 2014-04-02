#!/bin/bash

SERVERS="moxie-1 rootbeer-1"


DATE=`date '+%Y%m%d%H%M_%S'`
DEPLOYDIR="/htapps/catalog/releases/$DATE"
SYMLINKDIR="/htapps/catalog/webtest"

### DEBUG ###
SERVERS="moxie-1"
#SERVERS="waffle"
#DEPLOYDIR="/tmp/releases/$DATE"
#SYMLINKDIR="/tmp/web"

##############


TAG=$1

# The script to run
COMMANDS="
   cd $DEPLOYDIR;  
   tar xzf -;
   $DEPLOYDIR/derived_data/getall.sh  $DEPLOYDIR/derived_data/
   rm $SYMLINKDIR;
   ln -s $DEPLOYDIR $SYMLINKDIR;
   mkdir -p $DEPLOYDIR; 
";





function deploy() {
  git archive --format=tar $TAG | ssh $1 "mkdir -p $DEPLOYDIR && cd $DEPLOYDIR &&  tar xf -"
  ssh $1 <<EOF
  $DEPLOYDIR/derived_data/getall.sh  $DEPLOYDIR/derived_data/
  rm -f $SYMLINKDIR
  ln -s $DEPLOYDIR $SYMLINKDIR;
EOF
}

for server in $SERVERS; do 
  deploy $server;
done

