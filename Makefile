php-dependencies:
	composer install --no-interaction

js-dependencies:
	bower install --allow-root

dependencies: php-dependencies js-dependencies

migrations:
	php bin/console doctrine:migrations:migrate

test-migrations:
	php bin/console doctrine:migrations:migrate -n --env test

assets:
	php bin/console assets:install

dev-server-run:
	php bin/console server:run

test:
	php ./vendor/phpunit/phpunit/phpunit

travisci-packages:
	sudo apt-get update -qq
	sudo apt-get install -y php5-sqlite php5-gd sqlite3

travisci-before-script: travisci-packages php-dependencies test-migrations

travisci-script: test

travisci-after-success:
	[ "${TRAVIS_PULL_REQUEST}" = "false" ] && /bin/bash ./build/create-github-release.sh ${GITHUB_TOKEN} travisci-build-${TRAVIS_BRANCH}-${TRAVIS_BUILD_NUMBER} ${TRAVIS_COMMIT} https://travis-ci.org/journeymonitor/control/builds/${TRAVIS_BUILD_ID}

docker-shell:
	/bin/bash docker/shell-app-container.sh
