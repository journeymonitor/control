#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

. /etc/journeymonitor/app-control-env.sh

/usr/bin/php $DIR/console journeymonitor:control:import:testresults $1 --env=$2
