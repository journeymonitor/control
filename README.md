# Selenior - CONTROL - web-frontend

## About

Application for the service homepage


## Setup instructions

### Mac OS X

Assumes that you have PHP 5.5, Git, and Composer installed.

    git clone git@bitbucket.org:selenior/control-web-frontend.git
    cd control-web-frontend
    composer install
    php app/console doctrine:migrations:migrate
    php app/console server:run

### Ubuntu 14.04 64bit

First, set up the target machine as described in the *infra-maschine-provisioner* README.

    sudo su -
    cd /opt/selenior
    git clone git@bitbucket.org:selenior/control-web-frontend.git
    cd control-web-frontend
    composer install
    chown -R selenior app/cache
    chown -R selenior app/logs
    rm -rf app/cache/prod
    sudo -u selenior php app/console doctrine:migrations:migrate --env prod
    screen
    rm -rf app/cache/prod && chown -R selenior app/cache && chown -R selenior app/logs && sudo -u selenior php app/console server:run 127.0.0.1:5999 --env prod
    # Hit ctrl-a-d to leave screen

### Other info

At app/Resources/selenior-control.sqlite-dev.dist.gz is an sqlite3 database file that contains the User demo-user@journeymonitor.com with password 'demo123'.
The user has some testcases and testresult data. Simply unzip to /var/tmp/selenior-control.sqlite-dev.

## Development with docker

This project ships with some Dockerfiles for webserver and database that can be used for local development.

    cd docker && bash build.sh
    chmod a+x selenior-docker.sh
    sudo ln -s `pwd`/selenior-docker.sh /usr/local/bin/selenior-docker
    cd ..
    selenior-docker -xdebug start
    docker/shell sudo -uwww-data composer install
        
This will launch a contaier for the database and one for the web-application, linked to the database container.
XDebug will be enabled with idekey "xdebug" if you use the "-xdebug" flag.
Try clearing the cache to see that permissions are set correctly (the nginx will be run as www-data)

    docker/console cache:clear
    docker/console doctrine:database:create
    docker/console doctrine:schema:update --force
    docker/console assets:install

If everything works fine, you should be able to open `http://frontend.journeymonitor.local.net/` in your
browser.
For convience there is also a shortcut to get into the mysql-shell in the db container:

    selenior-docker mysql-console
