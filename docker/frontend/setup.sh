#!/bin/bash

if [ ! -e "/var/www/CNAME" ]
then
    echo "CNAME file not found. Starting without the domain name"
    VHOST="unknown"
else
	VHOST=$(head -n 1 /var/www/CNAME)
fi

echo "Setting up [$VHOST]"

if [ -e "/var/www/.nginx.conf" ]
then
    echo "Found virtual host config file for nginx"
    sed -e "s/%HOSTNAME%/$VHOST/g" /var/www/.nginx.conf > "/etc/nginx/sites-enabled/$VHOST"
fi

# copy default config with replaced domain name
cp /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
sed -e "s/%HOSTNAME%/$VHOST/g" -i /etc/nginx/sites-enabled/default


echo "Setting up PHP env"

# Enable error logs
sed -i 's|;php_flag\[display_errors\]\s*=\s*off|php_flag[display_errors] = on|g' /etc/php5/fpm/pool.d/www.conf

# Add envvars to PHP-FPM configuration files
envs=`printenv`

for env in $envs
do
    IFS== read name value <<< "$env"
    # FIX for PHP-FPM BUG http://ma.ttias.be/php-fpm-environment-variables-are-limited-to-1024-chars/
    if [ -n "$value" ] && [ ${#value} -lt 256 ]; then
        echo "env[$name] = \"$value\"" >> /etc/php5/fpm/php-fpm.conf
    fi
done

# xdebug
if [ "$ENABLE_XDEBUG" ]; then
	echo "Enabling xdebug"
	php5enmod xdebug
else
	echo "Disabling xdebug"
	php5dismod xdebug
fi

echo "Starting supervisor"

supervisord --nodaemon
