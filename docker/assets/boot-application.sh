#!/usr/bin/env bash

set -e

cd /opt/journeymonitor/control
chown -R www-data:www-data var/cache/ var/logs
sudo -u www-data composer install
sudo -u www-data php bin/console doctrine:migrations:migrate --no-interaction

/etc/init.d/rsyslog start
/usr/sbin/cron
/etc/init.d/php5-fpm start
/etc/init.d/nginx start

tail -f /dev/null # keep running
