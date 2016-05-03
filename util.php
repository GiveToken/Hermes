<?php
use Sizzle\Bacon\Connection;
require_once __DIR__.'/src/autoload.php';

function execute_query($sql)
{
    if ($result = Connection::$mysqli->query($sql)) {
        return $result;
    } else {
        error_log($sql);
        throw new Exception(Connection::$mysqli->error);
    }
}

function logged_in()
{
    return isset($_SESSION['user_id']);
}

function login_then_redirect_back_here()
{
    header('Location: '.BASE_URL."login&next=".urlencode($_SERVER['REQUEST_URI']));
}
