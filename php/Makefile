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
	sudo apt-get install -y sqlite3

travisci-before-script: travisci-packages php-dependencies test-migrations

travisci-script: test
