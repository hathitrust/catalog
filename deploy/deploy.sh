#!/bin/bash

SERVERS="macc-ht-web-133.umdl.umich.edu ictc-ht-web-206.umdl.umich.edu"
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
    ssh $server -T "chmod g+w $SYMLINKDIR/derived_data/*"
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
git tag --sort=taggerdate | tail -4
TAG=`git tag --sort=taggerdate | tail -1`
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

DEPLOY_TARBALL="/tmp/catalog_${DATE}_${TAG}.tar"
echo "DEPLOY_TARBALL is $DEPLOY_TARBALL"

# This assumes deploy.sh is being run from the root of the git repo.
# Should we add a test for the existence of `vendor/` and bail out if not found?
function make_archive() {
  git archive --format=tar $1 > $DEPLOY_TARBALL
  tar -rf $DEPLOY_TARBALL vendor
}

function deploy() {
  cat $DEPLOY_TARBALL | ssh $2 "mkdir -p $DEPLOYDIR && cd $DEPLOYDIR &&  tar xf -"
  ssh -T $2 <<EOF
  chmod g+rwx $DEPLOYDIR/derived_data
  chmod g+s $DEPLOYDIR/derived_data
  chmod -R g+rw $DEPLOYDIR/derived_data/*
  $DEPLOYDIR/derived_data/getall.sh   $DEPLOYDIR/derived_data
  chmod -R g+rw $DEPLOYDIR/derived_data/*
  mkdir $DEPLOYDIR/interface/compile
  chmod 777 $DEPLOYDIR/interface/compile
  rm -f $SYMLINKDIR
  ln -s $DEPLOYDIR $SYMLINKDIR;
EOF
}

make_archive $TAG

for server in $SERVERS; do 
  echo "Deploying to $server"
  deploy $TAG $server ;
done

echo "Removing $DEPLOY_TARBALL"
rm $DEPLOY_TARBALL
