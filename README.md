# Selenior - CONTROL - web-frontend

## About

Application for the service homepage


## Setup instructions

The following applies to a vanilla Ubuntu 14.04 64bit system.

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

## Development with docker

This project ships with some Dockerfiles for webserver and database that can be used for local development.

    cd docker && bash build.sh
    sudo ln -s `pwd`/selenior-docker.sh /usr/local/bin/selenior-docker
    cd ..
    selenior-docker -xdebug start
    docker/shell composer install
        
This will launch a contaier for the database and one for the web-application, linked to the database.
XDebug will be enabled with idekey "xdebug" if you use the "-xdebug" flag.
Try clearing the cache to see that permissions are set correctly (the nginx will be run as www-data)

    docker/console cache:clear
 
    
    