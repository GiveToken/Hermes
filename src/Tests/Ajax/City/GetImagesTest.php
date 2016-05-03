<?php
namespace Sizzle\Tests\Ajax\City;

use Sizzle\Bacon\Database\City;
/**
 * This class tests the ajax endpoint to get a city's images.
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Tests/Ajax/City/GetImagesTest
 */
class GetImagesTest
  extends \PHPUnit_Framework_TestCase
{
    /**
   * Requires the util.php file of functions
   */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    public function setUp()
    {
        // create a city
        $city = new City();
        $city->name = "City #" . rand();
        $city->population = rand(10000, 10000000);
        $city->longitude = rand(0, 100);
        $city->latitude = rand(0, 100);
        $city->county = "County " . rand(0, 100);
        $city->country = "CT";
        $city->timezone = "Awesome Standard Time";
        $city->temp_hi_spring = rand(0, 100);
        $city->temp_lo_spring = rand(0, 100);
        $city->temp_avg_spring = rand(0, 100);
        $city->temp_hi_summer = rand(0, 100);
        $city->temp_lo_summer = rand(0, 100);
        $city->temp_avg_summer = rand(0, 100);
        $city->temp_hi_fall = rand(0, 100);
        $city->temp_lo_fall = rand(0, 100);
        $city->temp_avg_fall = rand(0, 100);
        $city->temp_hi_winter = rand(0, 100);
        $city->temp_lo_winter = rand(0, 100);
        $city->temp_avg_winter = rand(0, 100);
        $city->save();
        $this->city = $city;

        // Add images to city
        $sql = "INSERT INTO `giftbox`.`city_image` (`city_id`, `image_file`) VALUES ('$city->id', 'AL/Ralph/3.svg');";
        execute_query($sql); // image #1
        execute_query($sql); // image #2
        execute_query($sql); // image #3
        execute_query($sql); // image #4
    }

    /**
   * Tests request via ajax endpoint.
   */
    public function testRequest()
    {
        // test created city
        $url = TEST_URL . "/ajax/city/get_images";
        $fields = array(
        'city_id'=>$this->city->id
        );
        $fields_string = "";
        foreach ($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_COOKIE, getTestCookie(true));
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertEquals('true', $return->success);
        $this->assertEquals(4, count($return->data));

        // grab it from the database
        $city_id = $this->city->id;
        $query = "SELECT * FROM city_image WHERE `city_id` = '$city_id'";
        $result = execute_query($query);
        $this->assertEquals(4, $result->num_rows);

        // lets grab one of the urls and make sure the image is public
        ob_start();
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $return->data[0]);
        //    curl_setopt ($ch2, CURLOPT_HEADER, true);
        $response = curl_exec($ch2);
        $this->assertEquals(true, $response);
        $info = curl_getinfo($ch2);
        ob_end_clean();
        $this->assertEquals(200, $info['http_code']);
        $this->assertEquals('image', substr($info['content_type'], 0, 5));
    }

    /**
   * Tests request failure via ajax endpoint.
   */
    public function testFail()
    {
        $url = TEST_URL . "/ajax/city/get_images";
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $json = ob_get_contents();
        $return = json_decode($json);
        $this->assertEquals('false', $return->success);
        $this->assertEquals('', $return->data);
        ob_end_clean();
    }

    /**
   * Tests request success via ajax endpoint with no cookie.
   */
    public function testRequestNoCookie()
    {
        // attempt curl request
        $url = TEST_URL . "/ajax/city/get_images";
        $fields = array(
        'city_id'=>$this->city->id
        );
        $fields_string = "";
        foreach ($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertEquals('true', $return->success);
        $this->assertEquals(4, count($return->data));

        // grab it from the database
        $city_id = $this->city->id;
        $query = "SELECT * FROM city_image WHERE `city_id` = '$city_id'";
        $result = execute_query($query);
        $this->assertEquals(4, $result->num_rows);
    }
}
?>
