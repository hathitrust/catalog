#!/bin/bash

SERVERS="moxie-1 rootbeer-1"
DEPLOYROOT="/htapps/catalog/releases"
SYMLINKDIR="/htapps/catalog/web"


### DEBUG ###
#SERVERS="waffle"
#DEPLOYROOT="/tmp/releases"
#SYMLINKDIR="/tmp/web"
##############


function verify_tag() {
  if [[ "$1" = `git describe --exact-match "$1" 2>/dev/null` ]]; then
    return 0; # true
  else
    return 1; # false
  fi
}

function rollback() {
  for i in $SERVERS; do
    RECENT=`ssh -T $i ls -1rt $DEPLOYROOT | tail -2 | head -1`
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
    ssh -T $server <<EOF
      rm -f $SYMLINKDIR
      ln -s $ROLLBACKDIR $SYMLINKDIR;
EOF
  done;
}


function re_derive_data() {
  for server in $SERVERS; do
    echo "Deriving data for $server"
    ssh $server -T "$SYMLINKDIR/derived_data/getall.sh"
  done
}

if [[ $1 = 'derive_data' ]]; then
  re_derive_data
  exit
fi

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
read -n 1 -r -p "Using tag $TAG. OK? (Y/N) "

echo

if [[ $REPLY =~ ^[Yy]$ ]]
then
  TAG=$TAG; # no op
else
  read -r -p "What tag to use? > "
  if verify_tag $REPLY; then
    TAG=$REPLY;
    echo "Using tag $TAG"
  else
    echo "Tag $REPLY does not exist. Aborting."
    exit;
  fi
fi


# Set the deploy directory
DATE=`date '+%Y%m%d%H%M'`
DEPLOYDIR="${DEPLOYROOT}/${DATE}_${TAG}"
echo "DEPLOYDIR is $DEPLOYDIR"

function deploy() {
  git archive --format=tar $1 | ssh $2 "mkdir -p $DEPLOYDIR && cd $DEPLOYDIR &&  tar xf -"
  ssh -T $2 <<EOF
  $DEPLOYDIR/derived_data/getall.sh   $DEPLOYDIR/derived_data
  mkdir $DEPLOYDIR/interface/compile
  chmod 777 $DEPLOYDIR/interface/compile
  rm -f $SYMLINKDIR
  ln -s $DEPLOYDIR $SYMLINKDIR;
EOF
}



for server in $SERVERS; do 
  echo "Deploying to $server"
  deploy $TAG $server ;
done

