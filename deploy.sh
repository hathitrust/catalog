#!/bin/bash

SERVERS="moxie-1 rootbeer-1"


DATE=`date '+%Y%m%d%H%M_%S'`
DEPLOYROOT="/htapps/catalog/releases"
DEPLOYDIR="$DEPLOYROOT/$DATE"


SYMLINKDIR="/htapps/catalog/web"


### DEBUG ###
#SERVERS="waffle"
#DEPLOYDIR="/tmp/releases/$DATE"
#SYMLINKDIR="/tmp/web"
#SYMLINKDIR="/htapps/catalog/webtest"
##############


function rollback() {
  for i in $SERVERS; do
    RECENT=`ssh $i ls -1rt $DEPLOYROOT | tail -2 | head -1`
    echo "Most recent deploys"
    echo
    ssh $i ls -1rt $DEPLOYROOT 
    echo
    read -r -p "Roll back to when (default: $RECENT) "
    if [[ -z $REPLY ]]; then
      ROLLBACK=$RECENT;
    else
      ROLLBACK=$REPLY
    fi;
    ROLLBACKDIR="$DEPLOYROOT/$ROLLBACK"
    echo "Choosing to roll back to $ROLLBACKDIR"
    echo "Will link $SYMLINKDIR to $ROLLBACKDIR"
    echo
    echo
    break
  done
  for server in $SERVERS; do
    echo "Working on $server"
    ssh $server <<EOF
      rm -f $SYMLINKDIR
      ln -s $ROLLBACKDIR $SYMLINKDIR;
EOF
  done;
}


if [[ $1 = 'rollback' ]]; then
  rollback
  exit
fi

echo
echo Latest tags:
git tags | tail -4
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

