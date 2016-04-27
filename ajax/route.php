<?php
/**
 * Routes the AJAX reuqest
 */

// Parse URI
$pieces = explode('?', $_SERVER['REQUEST_URI']);
$endpoint = $pieces[0];
$endpoint_parts = explode('/', $endpoint);
if (isset($pieces[1])) {
    $gets = $pieces[1];
    $gets = explode('&', $gets);
    $get_parts = array();
    foreach ($gets as $get) {
        $parts = explode('=', $get);
        $get_parts[$parts[0]] = $parts[1];
    }
}

// redirect if not login
if (!logged_in() && 'login' != $endpoint_parts[2]) {
  header('Location: '.'/');
}

// Execute script if exists
if (isset($endpoint_parts[2])) {
    $file1 = __DIR__.'/'.$endpoint_parts[2] . '.php';
    if (isset($endpoint_parts[3])) {
        $file2 = __DIR__.'/'.$endpoint_parts[2] . '/' . $endpoint_parts[3] . '.php';
        if (isset($endpoint_parts[4])) {
            $file3 = __DIR__.'/'.$endpoint_parts[2] . '/';
            $file3 .= $endpoint_parts[3]  . '/' . $endpoint_parts[4] . '.php';
        }
    }
}
if (isset($file1) && file_exists($file1)) {
    include $file1;
} elseif (isset($file2) && file_exists($file2)) {
    include $file2;
} elseif (isset($file3) && file_exists($file3)) {
    include $file3;
}
