<?php
use Sizzle\Bacon\Connection;

// always run tests in strict mode
error_reporting(E_ALL);

// local settings
require_once __DIR__.'/local.php';

// autoloading
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../autoload.php';

// connect to database
new Sizzle\Bacon\Connection();

//load functions
require_once __DIR__.'/../../util.php';

/**
 * This is a testing function for obtaining a valid cookie.
 *
 * @return string - a cookie to access a logged in session
 */
function getTestCookie()
{
    $password = 'nachos';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $username = rand().'@gosizzle.io';
    $query = "INSERT INTO user (first_name, last_name, email_address, password, admin)
              VALUES ('fake', 'user', '$username', '$hash', 'Y')";
    execute_query($query);
    $id = Connection::$mysqli->insert_id;
    $ch = curl_init(TEST_URL . '/ajax/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "login_email=$username&password=$password&login_type=EMAIL");
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
    preg_match_all('/^Set-Cookie: PHPSESSID=(.*?);/mi', $result, $matches);
    $userCookie = $matches[1][0];
    return 'PHPSESSID=' . $userCookie . ';';
}
define('TEST_COOKIE', getTestCookie());
if (!defined('FILE_STORAGE_PATH')) {
    define('FILE_STORAGE_PATH', 'uploads/');
}
