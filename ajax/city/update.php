<?php
use Sizzle\Bacon\Database\City;

$success = 'false';
$data = '';
if (logged_in()) {

    // create City
    $City = new City($_POST['city_id'] ?? '');
    if ($City->id > 0) {
        $vars = [
           'name',
           'population',
           'longitude',
           'latitude',
           'county',
           'country',
           'timezone',
           'temp_hi_spring',
           'temp_lo_spring',
           'temp_avg_spring',
           'temp_hi_summer',
           'temp_lo_summer',
           'temp_avg_summer',
           'temp_hi_fall',
           'temp_lo_fall',
           'temp_avg_fall',
           'temp_hi_winter',
           'temp_lo_winter',
           'temp_avg_winter'
        ];

        $missing_var = false;
        foreach ($vars as $var) {
            if (isset($_POST[$var]) && '' != $_POST[$var]) {
                $City->$var = $_POST[$var];
            } else {
                $missing_var = true;
            }
        }
        if (!$missing_var) {
            $success = $City->save() ? 'true' : 'false';
        }
    }
}

header('Content-Type: application/json');
echo json_encode(array('success'=>$success, 'data'=>$data));
