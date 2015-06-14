php-dependencies:
	composer install --no-interaction

js-dependencies:
	bower install --allow-root

dependencies: php-dependencies js-dependencies

test-migrations:
	php app/console doctrine:migrations:migrate -n --env test

assets:
	php app/console assets:install

test:
	php ./vendor/phpunit/phpunit/phpunit

travisci-packages:
	sudo apt-get update -qq
	sudo apt-get install -y php5-sqlite php5-gd sqlite3

travisci-before-script: travisci-packages php-dependencies assets test-migrations

travisci-script: test

travisci-after-success:
	/bin/bash ./build/create-github-release.sh ${GITHUB_TOKEN} travisci-build-${TRAVIS_BRANCH}-${TRAVIS_BUILD_NUMBER} ${TRAVIS_COMMIT} https://travis-ci.org/journeymonitor/control/builds/${TRAVIS_BUILD_ID}
