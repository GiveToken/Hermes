<?php
namespace Sizzle\Tests\Api;

use Sizzle\Bacon\Database\User;

/**
 * This class tests API authentication
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Tests/Api/AuthTest
 */
class AuthTest
extends \PHPUnit_Framework_TestCase
{
    protected $testURL;
    protected $email;
    protected $apiKey;
    protected $user;

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        //include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Sets up URL & user with API access.
     */
    public function setUp()
    {
        $this->testURL = 'api.gosizzle.local/';
        $this->email = rand() . '@gossizle.io';
        $this->apiKey = rand() . '_key';
        $sql = "INSERT INTO user (email_address, api_key)
                VALUES ('{$this->email}', '{$this->apiKey}')";
        $userId = insert($sql);
        $this->user = (new User())->fetch($this->apiKey, 'api_key');
    }

    /**
     * Tests API access fail due to invalid user.
     */
    public function testInvalidUser()
    {
        //bogus api key
        $fields = array(
            'api_key'=>'garbage'
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
        curl_setopt($ch, CURLOPT_URL, $this->testURL);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertFalse($return->success);
        $this->assertEquals('', $return->data);
        $this->assertEquals('Invalid User', $return->errors[0]);

        // no api key
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_URL, $this->testURL);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertFalse($return->success);
        $this->assertEquals('', $return->data);
        $this->assertEquals('Invalid User', $return->errors[0]);
    }
}
?>
