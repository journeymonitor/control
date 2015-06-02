# Selenior - CONTROL - web-frontend

## About

Application for the service homepage


## Setup instructions

### Mac OS X

Assumes that you have PHP 5.5, Git, Bower, and Composer installed.

    git clone git@bitbucket.org:selenior/control-web-frontend.git
    cd control-web-frontend
    composer install
    bower install
    php app/console doctrine:migrations:migrate
    php app/console assets:install
    php app/console server:run

### Ubuntu 14.04 64bit

First, set up the target machine as described in the *infra-maschine-provisioner* README.

    sudo su -
    cd /opt/selenior
    git clone git@bitbucket.org:selenior/control-web-frontend.git
    cd control-web-frontend
    composer install
    bower install
    chown -R selenior app/cache
    chown -R selenior app/logs
    rm -rf app/cache/prod
    sudo -u selenior php app/console doctrine:migrations:migrate --env prod
    php app/console assets:install
    screen
    rm -rf app/cache/prod && chown -R selenior app/cache && chown -R selenior app/logs && sudo -u selenior php app/console server:run 127.0.0.1:5999 --env prod
    # Hit ctrl-a-d to leave screen

### Windows (tested on Windows 8.1 Pro x64 WMC)

    First install git: https://git-scm.com/download/win
    now set up User, SSH both locally and in bitbucket
    git clone git@bitbucket.org:selenior/control-web-frontend.git
    cd control-web-frontend
    now download php http://windows.php.net/download/#php-5.5 (x64 Threadsafe) and unzip to C:\Program Files\php
    now add php to your PATH Variable (Windows+Pause --> Advanced --> Environment Variables --> PATH (Edit / New) --> Add 'C:\Program Files\php;' without quotes)
    now copy 'C:\Program Files\php\php.ini-development' to 'C:\Program Files\php\php.ini'
    start an editor of your choice in elevated (admin) mode and make sure the following extensions are activated (remove ; in front):
        extension_dir = "ext"
        extension=php_curl.dll
        extension=php_mbstring.dll
        extension=php_openssl.dll
        extension=php_pdo_sqlite.dll
        extension=php_sqlite3.dll
        also add 'date.timezone = Europe/Berlin' to the file
    now open a cmd console and try to run 'php' - if you see no output at all thats's good!
    next you need to install composer https://getcomposer.org/download/
    now you need to cd into the cloned directory and run composer install
    now login or create a github user and stay logged in in your browser
    while running composer it will fail saying you need to create an auth token - it will generate a link for you redirecting you in your already logged in github account ;)
    after generating the auth token rerun composer install to get the BE vendor files
    now install node.js https://nodejs.org/download/
    make sure to let the installer add PATH variables
        if installer fails with error code 2503/2502 open the task Manager with CTRL+SHIFT+ESC
            - now head over to details and kill explorer.exe (your taskbar will die)
            - now go to File --> new Task and type in explorer.exe but make sure "run as admin" is checked
            - then rerun the installation process
    if node was installed sucessfully try "npm" in your console - if the command was not found try rebooting (yay Windows!)
    now head back in the checked out folder and run "npm install -g bower"
    then run "bower install" to get the FE vendor files
    now edit app/config/parameters.yml - set database_path: C:\Temp\selenior-control.sqlite
    now edit app/config/config.yml - set path: "C:\Temp\selenior-control.sqlite"
    DO NOT COMMIT THESE FILES
    
    php app/console doctrine:migrations:migrate
    php app/console assets:install
    php app/console server:run

### Other info

At app/Resources/selenior-control.sqlite-dev.dist.gz you'll find an sqlite3 database file that contains the user 'demo-user@journeymonitor.com' with password 'demo123'.
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
