# JourneyMonitor - CONTROL component

## About

Application that provides the JourneyMonitor website.


## Development setup instructions

### Using Vagrant (recommended)

See https://github.com/journeymonitor/infra/blob/master/README.md?at=master

Afterwards, come back here and continue at *Development workflow*.


### Mac OS X

Assumes that you have PHP 5.5, Git, Bower, and Composer installed.

    git clone git@github.com:journeymonitor/control.git
    cd control
    composer install
    bower install
    php app/console doctrine:migrations:migrate
    php app/console assets:install
    php app/console server:run


### Windows

We do not officially support installing and running this application on Windows environments, but the following might be
helpful if you want to give it a try. The described steps have been tested on Windows 8.1 Pro x64 WMC).

- First install git: https://git-scm.com/download/win
- `git clone git@github.com:journeymonitor/control.git`
- `cd control`
- Download PHP from http://windows.php.net/download/#php-5.5 (x64 Threadsafe) and unzip to `C:\Program Files\php`
- Add php to your PATH Variable (Windows+Pause --> Advanced --> Environment Variables --> PATH (Edit / New) --> Add 'C:\Program Files\php;' without quotes)
- Copy `C:\Program Files\php\php.ini-development` to `C:\Program Files\php\php.ini`
- Start an editor of your choice in elevated (admin) mode and make sure the following extensions are activated (remove `;` in front):
- `extension_dir = "ext"`
- `extension=php_curl.dll`
- `extension=php_mbstring.dll`
- `extension=php_openssl.dll`
- `extension=php_pdo_sqlite.dll`
- `extension=php_sqlite3.dll`
- Also add `date.timezone = Europe/Berlin` to the file
- Open a cmd console and try to run `php` - if you see no output at all thats's good!
- Next you need to install Composer from https://getcomposer.org/download/
- change into the cloned directory and run `composer install`
- Login or create a github user and stay logged in in your browser
- While running Composer it will fail saying you need to create an auth token - it will generate a link for you redirecting you in your already logged in github account
- After generating the auth token rerun `composer install` to get the backend vendor files
- Install Node.js from https://nodejs.org/download/
- Make sure to let the installer add PATH variables
- If Node.js was installed successfully try running `npm` in your console - if the command was not found try rebooting
- Head back to the checked out folder and run `npm install -g bower`
- Then run `bower install` to get the frontend vendor files
- Edit `app/config/parameters.yml` - set database_path: C:\Temp\journeymonitor-control.sqlite
- Edit `app/config/config.yml` - set path: "C:\Temp\journeymonitor-control.sqlite"    
- Run `php app/console doctrine:migrations:migrate`
- Run `php app/console assets:install`
- Run `php app/console server:run


### Docker

We do not officially support installing and running this application in a Docker environment,
but the following might be helpful if you want to give it a try.

The project ships with some Dockerfiles for webserver and database that can be used for local development.

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

### Other info

At app/Resources/selenior-control.sqlite-dev.dist.gz you'll find an sqlite3 database file that contains the user 'demo-user@journeymonitor.com' with password 'demo123'.
The user has some testcases and testresult data. Simply unzip to /var/tmp/selenior-control.sqlite-dev.


## Development workflow

*This assumes that you have set up a development environment with Vagrant*

- SSH into the development VM by running `vagrant ssh` from the *infra* folder
- `cd /opt/selenior/control`
- `composer install`
- `bower install`
- `php app/console doctrine:migrations:migrate`
- `php app/console assets:install --symlink`

You can now browse to http://192.168.99.99/. Run the tests via `php ./vendor/phpunit/phpunit/phpunit`.


## Styleguide

See http://paletton.com/#uid=33r0u0knJASdGPbjcHDs+wBvDpF for color scheme.
