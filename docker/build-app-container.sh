#!/usr/bin/env bash

# The full path to this script, no matter where it is called from
SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

cd $SCRIPTDIR

if [ -d "../../infra" ]
then
    echo "Found a checkout of journeymonitor/infra at $SCRIPTDIR/../../infra."
    echo "We are going to use it instead of cloning from Github."
    rm -rf assets/infra
    cp -a ../../infra assets/infra
else
    echo "Could not find a local checkout of journeymonitor/infra, cloning a fresh copy from Github."
    git clone --single-branch --depth 1 https://github.com/journeymonitor/infra.git assets/infra
fi

docker build -t journeymonitor/control:latest -f ./Dockerfile ../

rm -rf assets/infra
