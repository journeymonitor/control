# JourneyMonitor

## About this repository

Applications that powers the JourneyMonitor website at http://journeymonitor.com.

It contains two technical stacks, one based on PHP (e.g. the Symfony2 app), one based on JVM technology.

[![Build Status](https://travis-ci.org/journeymonitor/control.png?branch=master)](https://travis-ci.org/journeymonitor/control)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4929a5b2-4be3-46e2-8c6d-28d46fd0e862/big.png)](https://insight.sensiolabs.com/projects/4929a5b2-4be3-46e2-8c6d-28d46fd0e862)


## About the JourneyMonitor project

Please see [ABOUT.md](https://github.com/journeymonitor/infra/blob/master/ABOUT.md) for more information.


## Setting up a development environment

### Using Docker (recommended)

Set up a development environment as described [in this document](https://github.com/journeymonitor/infra/blob/master/README.md#setting-up-a-development-environment).

Afterwards, follow these steps:

- From `infra/docker`, run `docker-compose exec journeymonitor-control bash`
- `cd /opt/journeymonitor/control`

Enjoy. Consider running most commands via `sudo -u www-data`, because `www-data` is the owner of all files in
/opt/journeymonitor/control and the owner of the nginx and php-fpm processes.

Example:
- `cd /opt/journeymonitor/control/php`
- `sudo -u www-data make test`


### Mac OS X

We do not officially support installing and running this application on Mac OS X environments (outside of Docker), but
the following might be helpful if you want to give it a try.

Assumes that you have Make, PHP 5.5, Git, Bower, and Composer installed.

    git clone git@github.com:journeymonitor/control.git
    cd control/php
    make dependencies
    make migrations
    make assets
    make dev-server-run

You can now access the application at http://localhost:8000. Run the tests via `make tests`. Please note that you need
to increase the `memory_limit` in `php.ini` to `256M` to make the test run work.


### Windows

We do not officially support installing and running this application on Windows environments, but the following might be
helpful if you want to give it a try. The described steps have been tested on Windows 8.1 Pro x64 WMC.

- First install git: https://git-scm.com/download/win
- `git clone git@github.com:journeymonitor/control.git`
- `cd control`
- Download PHP from http://windows.php.net/download/#php-5.5 (x64 Threadsafe) and unzip to `C:\Program Files\php`
- Add php to your PATH Variable (Windows+Pause --> Advanced --> Environment Variables --> PATH (Edit / New) --> Add
  'C:\Program Files\php;' without quotes)
- Copy `C:\Program Files\php\php.ini-development` to `C:\Program Files\php\php.ini`
- Start an editor of your choice in elevated (admin) mode and make sure the following extensions are activated (remove
  `;` in front):
- `extension_dir = "ext"`
- `extension=php_curl.dll`
- `extension=php_mbstring.dll`
- `extension=php_openssl.dll`
- `extension=php_pdo_sqlite.dll`
- `extension=php_sqlite3.dll`
- Also add `date.timezone = Europe/Berlin` to the file
- Open a cmd console and try to run `php` - if you see no output at all thats's good!
- Next you need to install Composer from https://getcomposer.org/download/
- Change into the cloned directory, subfolder `php`, and run `composer install` - choose
  `C:\Temp\journeymonitor-control` as the database file path
- Login or create a github user and stay logged in in your browser
- While running Composer it will fail saying you need to create an auth token - it will generate a link for you
  redirecting you in your already logged in github account
- After generating the auth token rerun `composer install` to get the backend vendor files
- Install Node.js from https://nodejs.org/download/
- Make sure to let the installer add PATH variables
- If Node.js was installed successfully try running `npm` in your console - if the command was not found try rebooting
- Head back to the checked out folder and run `npm install -g bower`
- Then run `bower install` to get the frontend vendor files
- Run `php app/console doctrine:migrations:migrate`
- Run `php app/console assets:install`
- Run `php app/console server:run`


### Other info

At `php/app/Resources/journeymonitor-control-dev.sqlite3.dist.gz` you'll find an sqlite3 database file that contains the
user 'demo-user@journeymonitor.com' with password 'demo123'. The user has some testcases and testresult data. Simply
unzip to `/var/tmp/journeymonitor-control-dev.sqlite3` if you want to use it.


## Web Frontend Styleguide

See http://paletton.com/#uid=33r0u0knJASdGPbjcHDs+wBvDpF for color scheme.
