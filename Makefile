php-dependencies:
	composer install --no-interaction

js-dependencies:
	bower install --allow-root

dependencies: php-dependencies js-dependencies

migrations:
	php app/console doctrine:migrations:migrate

test-migrations:
	php app/console doctrine:migrations:migrate -n --env test

assets:
	php app/console assets:install

dev-server-run:
	php app/console server:run

test:
	php ./vendor/phpunit/phpunit/phpunit

travisci-packages:
	sudo apt-get update -qq
	sudo apt-get install -y php5-sqlite php5-gd sqlite3

travisci-before-script: travisci-packages php-dependencies test-migrations
	echo 'date.timezone = "Europe/Paris"' >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
	~/.phpenv/versions/$(phpenv version-name)/bin/composer self-update

travisci-script: test

travisci-after-success:
	/bin/bash ./build/create-github-release.sh ${GITHUB_TOKEN} travisci-build-${TRAVIS_BRANCH}-${TRAVIS_BUILD_NUMBER} ${TRAVIS_COMMIT} https://travis-ci.org/journeymonitor/control/builds/${TRAVIS_BUILD_ID}
