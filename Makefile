php-dependencies:
	composer install --no-interaction

js-dependencies:
	/usr/local/bin/bower install --allow-root

dependencies: php-dependencies js-dependencies

migrations:
	/usr/bin/php app/console doctrine:migrations:migrate

test-migrations:
	/usr/bin/php app/console doctrine:migrations:migrate -n --env test

assets:
	/usr/bin/php app/console assets:install

dev-server-run:
	/usr/bin/php app/console server:run

test:
	/usr/bin/php ./vendor/phpunit/phpunit/phpunit

travisci-packages:
	/usr/bin/sudo /usr/bin/apt-get update -qq
	/usr/bin/sudo /usr/bin/apt-get install -y php5-sqlite php5-gd sqlite3

travisci-before-script: travisci-packages php-dependencies assets test-migrations

travisci-script: test

travisci-after-success:
	/bin/bash ./build/create-github-release.sh ${GITHUB_TOKEN} travisci-build-${TRAVIS_BRANCH}-${TRAVIS_BUILD_NUMBER} ${TRAVIS_COMMIT} https://travis-ci.org/journeymonitor/control/builds/${TRAVIS_BUILD_ID}
