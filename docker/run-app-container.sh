#!/usr/bin/env bash

# The full path to this script, no matter where it is called from
SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

docker stop journeymonitor-control 2> /dev/null
docker rm journeymonitor-control 2> /dev/null

docker run -d \
    -v $SCRIPTDIR/../:/opt/journeymonitor/control \
    -v /opt/journeymonitor/control/var/cache \
    -v /opt/journeymonitor/control/var/logs \
    -v $SCRIPTDIR/assets:/root/docker-assets \
    -v $SCRIPTDIR/../../infra:/root/docker-assets/infra \
    -p 8080:80 \
    -p 8082:8082 \
    --net journeymonitor \
    --name journeymonitor-control \
    journeymonitor/control:latest /bin/bash /root/docker-assets/boot-application.sh
