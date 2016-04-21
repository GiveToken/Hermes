<?php
use Sizzle\Bacon\Connection;
require_once __DIR__.'/src/autoload.php';

function escape_string($string)
{
    return Connection::$mysqli->real_escape_string($string);
}

function execute_query($sql)
{
    if ($result = Connection::$mysqli->query($sql)) {
        return $result;
    } else {
        error_log($sql);
        throw new Exception(Connection::$mysqli->error);
    }
}

function execute($sql)
{
    if (!Connection::$mysqli->query($sql)) {
        error_log($sql);
        throw new Exception(Connection::$mysqli->error);
    }
}

function insert($sql)
{
    if (!Connection::$mysqli->query($sql)) {
        error_log($sql);
        throw new Exception(Connection::$mysqli->error);
    }
    return Connection::$mysqli->insert_id;
}

function update($sql)
{
    if (!Connection::$mysqli->query($sql)) {
        error_log($sql);
        throw new Exception(Connection::$mysqli->error);
    }
    return Connection::$mysqli->affected_rows;
}

function logged_in()
{
    return isset($_SESSION['user_id']);
}

function login_then_redirect_back_here()
{
    header('Location: '.APP_URL."email_signup?action=login&next=".urlencode($_SERVER['REQUEST_URI']));
}
