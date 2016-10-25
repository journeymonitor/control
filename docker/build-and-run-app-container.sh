#!/usr/bin/env bash

# The full path to this script, no matter where it is called from
SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

/usr/bin/env bash ${SCRIPTDIR}/build-app-container.sh
/usr/bin/env bash ${SCRIPTDIR}/run-app-container.sh
