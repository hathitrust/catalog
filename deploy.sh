#!/bin/bash

SERVERS="moxie-1 rootbeer-1"


DATE=`date '+%Y%m%d%H%M_%S'`
DEPLOYDIR="/htapps/catalog/releases/$DATE"
SYMLINKDIR="/htapps/catalog/web"

### DEBUG ###
#SERVERS="waffle"
#DEPLOYDIR="/tmp/releases/$DATE"
#SYMLINKDIR="/tmp/web"

##############

echo
echo Latest tag:
git tags | tail -1
TAG=`git describe --abbrev=0`
echo
echo 
read -n 1 -r -p "Use tag $TAG? (Y/N) "

echo

if [[ $REPLY =~ ^[Yy]$ ]]
then
  TAG=$TAG; # no op
else
  echo "Aborting..."
  exit
fi



function deploy() {
  git archive --format=tar $TAG | ssh $1 "mkdir -p $DEPLOYDIR && cd $DEPLOYDIR &&  tar xf -"
  ssh $1 <<EOF
  $DEPLOYDIR/derived_data/getall.sh  $DEPLOYDIR/derived_data/
  mkdir $DEPLOYDIR/interface/compile
  chmod 777 $DEPLOYDIR/interface/compile
  rm -f $SYMLINKDIR
  ln -s $DEPLOYDIR $SYMLINKDIR;
EOF
}

for server in $SERVERS; do 
  deploy $server;
done

