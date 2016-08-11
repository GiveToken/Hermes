<?php
use Monolog\{
    ErrorHandler,
    Handler\SlackHandler,
    Logger
};
use Sizzle\Bacon\{
    Connection,
    Database\WebRequest
};

// set release version in /version file
define('VERSION', trim(file_get_contents(__DIR__.'/version')));

// autoload classes
require_once __DIR__.'/src/autoload.php';
require_once __DIR__.'/vendor/autoload.php';

//load functions
require_once __DIR__.'/util.php';

// Determine environment & fix URL as needed
$server = $_SERVER['SERVER_NAME'] ?? null;
if ('gosizzle.io' == strtolower($server) || 'givetoken.com' == strtolower($server) || 'www.givetoken.com' == strtolower($server)) {
    $url = 'https://hermes.gosizzle.io'.$_SERVER['REQUEST_URI'];
    header("Location: $url ", true, 301);
}
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'hermes.gosizzle.io' == $server ? 'production' : ('127.0.0.1' == $_SERVER['SERVER_ADDR'] ? 'local' : 'development'));
}

// check IP whitelist
/*if (ENVIRONMENT == 'production') {
    $whitelist = [
        '68.53.5.230',
        '64.134.191.49',
        '216.0.49.162',
        '23.24.238.222', //Central Ave Office
        '69.138.145.106', //Gary's House
        '68.53.54.92',
        '174.49.55.29', //Clocktower Drive
        '68.53.147.233', //Robbie's
        '172.12.62.168', // Suzanne's
        '98.87.154.138' // Vitor
    ];
    if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
        header('Location: https://www.gosizzle.io');
    }
}*/

// setup Monolog error handler to report to Slack
// this causes a 500 error on AWS (is PHP 7 issue?)
if (!defined('SLACK_TOKEN')) {
    define('SLACK_TOKEN', 'xoxb-17521146128-nHU6t4aSx7NE0PYLxKRYqmjG');
}
$logger = new Logger('bugs');
if (ENVIRONMENT != 'local') {
  if (ENVIRONMENT != 'production') {
      $name = 'Dev Hermes: '.$_SERVER['REQUEST_URI'];
      $slackHandler = new SlackHandler(SLACK_TOKEN, '#bugs-staging', $name, false);
  } else {
      $name = 'Production Hermes: '.$_SERVER['REQUEST_URI'];
      $slackHandler = new SlackHandler(SLACK_TOKEN, '#bugs', $name, false);
  }
  $slackHandler->setLevel(Logger::DEBUG);
  $logger->pushHandler($slackHandler);
  ErrorHandler::register($logger);
}

$prefix = "http://";
$use_https = false;
$socket = null;
if (ENVIRONMENT == 'production') {
    $stripe_secret_key = 'sk_live_MuUj2k3WOTpvIgw8oIHaON2X';
    $stripe_publishable_key = 'pk_live_AbtrrWvSZZCvLYdiLohHC2Kz';
} else {
    $stripe_secret_key = 'sk_test_RTcVNjcQVfYNiPCiY5O9CevV';
    $stripe_publishable_key = 'pk_test_Gawg5LhHJ934MPXlvglZrdnL';
}
if (isset($_SERVER['HTTPS'])) {
    if ($_SERVER['HTTPS'] === "on") {
        $prefix = "https://";
        $use_https = true;
    } else {
        if('hermes.gosizzle.io' == $server) {
            $url = 'https://hermes.gosizzle.io'.$_SERVER['REQUEST_URI'];
            header("Location: $url ", true, 301);
        }
    }
}

if (file_exists(__DIR__.'/../Giftbox/public/uploads')) {
  // development
  $file_storage_path = __DIR__.'/../Giftbox/public/uploads/';
} elseif (file_exists(__DIR__.'/../gosizzle.io/public/uploads')) {
  //production
  $file_storage_path = __DIR__.'/../gosizzle.io/public/uploads/';
} else {
  // mnimalist development?
  $file_storage_path = 'uploads/';
}

if (!defined('STRIPE_SECRET_KEY')) {
    define('STRIPE_SECRET_KEY', $stripe_secret_key);
}
if (!defined('STRIPE_PUBLISHABLE_KEY')) {
    define('STRIPE_PUBLISHABLE_KEY', $stripe_publishable_key);
}
if (!defined('FILE_STORAGE_PATH')) {
    define('FILE_STORAGE_PATH', $file_storage_path);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $prefix.$server.'/');
}

if (!defined('APP_URL')) {
    if (ENVIRONMENT == 'production') {
        define('APP_URL', 'https://www.gosizzle.io/');
    } else {
        // local if not on production
        define('APP_URL', 'http://gosizzle.local/');
    }
}

// connect to database
include __DIR__.'/config/credentials.php';
$mysqli = new mysqli($mysql_server, $user, $password, $database);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
new Connection($mysqli);

// start session
session_start();

// see if any session reports are stale
if (isset($_SESSION['report']) && is_array($_SESSION['report'])) {
    foreach ($_SESSION['report'] as $name => $report) {
        if (!isset($report['cached']) || $report['cached'] < time() - 7*24*60*60) {
            unset($_SESSION['report'][$name]);
        }
    }
}

// record website hit
if (isset($_COOKIE, $_COOKIE['visitor'])) {
    $visitor_cookie = $_COOKIE['visitor'];
} else {
    $visitor_cookie = substr(md5(microtime()), rand(0, 26), 20);
}
// set or reset cookie to expire in a year
setcookie('visitor', $visitor_cookie, time()+60*60*24*365);
$webRequest = new WebRequest();
$webRequest->visitor_cookie = $visitor_cookie;
if (isset($_SESSION, $_SESSION['user_id'])) {
    $webRequest->user_id = $_SESSION['user_id'];
}
if (isset($_SERVER)) {
    $webRequest->host = ($_SERVER['HTTP_HOST'] ?? '');
    $webRequest->user_agent = ($_SERVER['HTTP_USER_AGENT'] ?? '');
    $webRequest->uri = ($_SERVER['REQUEST_URI'] ?? '');
    $webRequest->remote_ip = ($_SERVER['REMOTE_ADDR'] ?? '');
    $webRequest->script = ($_SERVER['SCRIPT_NAME'] ?? '');
    $webRequest->save();
}
