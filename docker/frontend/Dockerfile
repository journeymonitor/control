FROM selenior/base

MAINTAINER Selenior “dev@journeymonitor.com”

# Install nginx
RUN add-apt-repository ppa:nginx/stable

RUN apt-get update && apt-get install -y --force-yes \
	nginx \
	memcached \
	git \
	tree \
	php5-fpm php5-cli \
    php5-dev php-pear php5-mysql php5-json php5-mcrypt php5-gd php5-sqlite php5-curl \
    php5-intl php5-imagick php5-redis php5-apcu php5-memcached php5-xdebug 

# Configure nginx to run it in non-daemonized mode
RUN echo "\ndaemon off;" >> /etc/nginx/nginx.conf
COPY ./nginx/default /etc/nginx/sites-available/default

# Enable error logs
RUN sed -i 's|;php_flag\[display_errors\]\s*=\s*off|php_flag[display_errors] = on|g' /etc/php5/fpm/pool.d/www.conf

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin && mv /usr/local/bin/composer.phar /usr/local/bin/composer

# Upgrade pear, install phpunit, phpmd & cs
RUN pear channel-update pear.php.net && \
	pear upgrade pear && \ 
	pear install PHP_CodeSniffer-2.0.0RC2 && \
	pear channel-discover pear.phpmd.org && \
	pear channel-discover pear.pdepend.org && \
	pear install --alldeps phpmd/PHP_PMD && \
	composer global require "phpunit/phpunit=4.3.*"

# Copy default config files
COPY ./php5/fpm/php-fpm.conf /etc/php5/fpm/php-fpm.conf
COPY ./php5/xdebug.ini /etc/php5/mods-available/xdebug.ini
COPY ./supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Shared volume
RUN mkdir -p /var/www
VOLUME ["/var/www", "/root/.ssh"]
RUN usermod -u 1000 www-data
RUN groupdel staff
RUN groupmod -g 50 www-data


COPY setup.sh /root/setup.sh
RUN chmod +x /root/setup.sh

WORKDIR /var/www

# changed path folder
#$$RUN export PATH=$PATH:/root/.composer/vendor/bin/
env PATH /root/.composer/vendor/bin/:$PATH

# Default command for container, start supervisor
CMD ["/bin/bash", "/root/setup.sh"]
USER root

# Expose ports
EXPOSE 80
EXPOSE 8080

