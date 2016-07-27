# Hermes

The S!zzle admin portal

## Table of Contents
1. [Set Up](#set-up)
1. [Branching Strategy](#branching)
1. [Frontend Direction](#frontend)
1. [Testing](#testing)
1. [Deployment](#deployment)

## <a id="set-up"></a>Set Up

### URLs
(hosted on AWS)
- production: [hermes.gosizzle.io](https://hermes.gosizzle.io/)
- development/staging: [dev.gosizzle.io](http://dev.gosizzle.io)


### Github

Add (Bacon)[https://github.com/GiveToken/Bacon] to your `src` after forking it:

    cd src
    git clone https://github.com/<your username>/Bacon.git

### AWS

If you'll be testing AWS, you'll need to create `/.aws/credentials` and enter the following:

    [sizzle]
    aws_access_key_id = AWS_ACCESS_KEY_ID
    aws_secret_access_key = AWS_SECRET_ACCESS_KEY

with your specific credentials.

### Apache

If on a Mac, you can update `/etc/apache2/extra/httpd-vhosts.conf` to include something like

    <VirtualHost *:80>
        ServerAdmin username@gosizzle.io
        ServerName hermes.gosizzle.local
        DocumentRoot "/Library/Webserver/Documents/Hermes/public"

        <Directory "/Library/Webserver/Documents/Hermes/public">
            Options -Indexes +FollowSymLinks +MultiViews
            AllowOverride All
            Require all granted
        </Directory>

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn
    </VirtualHost>

and restart Apache. Then modify `/etc/hosts` to include

    127.0.0.1       hermes.gosizzle.local

### <a id="composer"></a>Composer

[Composer](https://getcomposer.org/) is the PHP package manager used to bring in
3rd party PHP code. Once you have it in installed, cd to the project directory and
run

    composer install

which will create everything you need in the untracked vendor directory.

### <a id="bower"></a>Bower

[Bower](http://bower.io/) is a package manager used to bring in Polymer
components. Once you have it in installed, cd to the project directory and
run

    bower install

which will create everything you need in the untracked components directory.

## <a id="branching"></a>Branching Strategy

1) Fork  
2) Clone  
3) Checkout `develop`  
4) Branch  
5) Code  
6) Submit Pull Request  

### Handling Merge Conflicts

Github has a great reference [here](https://help.github.com/articles/resolving-a-merge-conflict-from-the-command-line/).

## <a id="testing"></a>Testing

Presuming you have set up [Composer](#composer), then PHPUnit will be available
in your /vendor/bin directory. You'll need to setup your local parameters by

    cp src/Tests/local.php.example src/Tests/local.php

and making any necessary changes to `local.php`. To run all the tests, just reference the
configuration file:

    vendor/bin/phpunit --bootstrap src/tests/autoload.php -c tests.xml

To also investigate the code coverage of the tests, you'll need the
[Xdebug PHP extension](http://xdebug.org/docs/install).
Make sure you put any unit tests in the `src/tests` directory and name them like
MyAwesomeTest.php.

For JavaScript testing, run the following command

    npm run test

## <a id="deployment"></a>Deployment

### Build Script
The build script (`build.sh`) runs unit tests, warns you of any untracked or
uncommited files, minifies JavaScript & CSS.
The full set of options is available in the help menu

    ./build.sh -h

The important caveat is that this script was written on OSX and may not work on
Cygwin or your favorite Windows version of Linux.

### <a id="deploy-staging"></a>Deploy to Staging

Any push to `develop` will one day be automagically pulled onto the staging server except
during the QA period for new releases.

### <a id="deploy-production"></a>Deploy to Production

Log into the production webserver and

    git branch YYYYMMDD.backup
    git pull origin master
    composer install
