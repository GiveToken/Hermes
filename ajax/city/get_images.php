<?php
use Sizzle\Bacon\Database\CityImage;

$success = 'false';
$data = '';
if (isset($_POST['city_id']) && '' != $_POST['city_id']) {
    $HOST_NAME = "http://city.images.s3-us-west-2.amazonaws.com/";
    $imageUrls = (new CityImage())->getAllImageUrlsForCity($_POST['city_id']);
    $data = array();
    foreach ($imageUrls as $url) {
        $fullUrl = $HOST_NAME.$url->image_file;
        $data[count($data)] = $fullUrl;
    }
    $success = 'true';
}

header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
