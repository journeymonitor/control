travisci-after-success:
	[ "${TRAVIS_PULL_REQUEST}" = "false" ] && /bin/bash ./build/create-github-release.sh ${GITHUB_TOKEN} travisci-build-${TRAVIS_BRANCH}-${TRAVIS_BUILD_NUMBER} ${TRAVIS_COMMIT} https://travis-ci.org/journeymonitor/control/builds/${TRAVIS_BUILD_ID}
