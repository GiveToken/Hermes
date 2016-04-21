<?php
use Aws\S3\S3Client;
use Sizzle\Bacon\Database\{
    City,
    CityImage
};
$bucket = 'city.images';

$success = 'false';
$data = '';
if (logged_in()) {
    $City = new City($_POST['city_id'] ?? '');
    if ($City->id > 0 && 4 == count($_FILES)) {
        // city exists! time to replace images...

        // assemble valid replacements
        $validUploads = [];
        foreach ($_FILES as $index=>$upload) {
            if (0 == $upload['error'] && 0 < $upload['size']) {
                $validUploads[ltrim($index, 'image-file-')] = $upload;
            }
        }

        // proceed if valid replacements
        foreach ($validUploads as $uploadIndex=>$upload) {
            // connect to S3 (once)
            $s3Client = $s3Client ?? S3Client::factory(array(
                'profile' => 'sizzle',
                'region'  => 'us-west-2',
                'version' => '2006-03-01'
            ));

            // Get the db objects (once)
            $cities = $cities ?? $City->getCityImages();

            // match db object
            for ($i = 0; $i < count($cities); $i++) {
                if (strpos($cities[$i]->image_file, '/'.$uploadIndex.'.') !== false) {
                    $cityImageToUpdate = $cities[$i];
                }
            }

            if (isset($cityImageToUpdate)) {
                // update S3 & database

                // delete old S3 object (should exist)
                $s3Client->deleteObject(array(
                    'Bucket' => $bucket,
                    'Key'    => $cityImageToUpdate->image_file
                ));

                // create new public S3 object
                $newKey = preg_replace('/\\.[^.\\s]{3,4}$/', '', $cityImageToUpdate->image_file);
                $newKey .= '.'.ltrim($upload['type'], 'image/');
                $result = $s3Client->putObject(array(
                    'Bucket'       => $bucket,
                    'Key'          => $newKey,
                    'SourceFile'   => $upload['tmp_name'],
                    'ContentType'  => $upload['type'],
                    'ACL'          => 'public-read',
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                ));

                // update database
                $cityImageToUpdate->image_file = $newKey;
                $cityImageToUpdate->save();
                $success = 'true';
            } else {
                // create S3 & database items

                // create new public S3 object
                // assumes of form "City Name, ST"
                $comma = strpos($City->name, ',');
                $city = substr($City->name, 0, $comma);
                $state = substr($City->name, $comma+1);
                $newKey = trim($state).'/'.str_replace(' ', '_', $city);
                $newKey .= '/'.$uploadIndex;
                $newKey .= '.'.ltrim($upload['type'], 'image/');
                $result = $s3Client->putObject(array(
                    'Bucket'       => $bucket,
                    'Key'          => $newKey,
                    'SourceFile'   => $upload['tmp_name'],
                    'ContentType'  => $upload['type'],
                    'ACL'          => 'public-read',
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                ));

                // insert into database
                $cityImage = new CityImage();
                $cityImage->city_id = $City->id;
                $cityImage->image_file = $newKey;
                $cityImage->save();
                $success = 'true';
            }
        }
    }
}
header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
