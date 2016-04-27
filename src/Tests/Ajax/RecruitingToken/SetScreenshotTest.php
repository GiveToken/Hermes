<?php
namespace Sizzle\Tests\Ajax\RecruitingToken;

use \Sizzle\Bacon\Database\{
    RecruitingTokenImage
};

/**
 * This class tests the ajax endpoint to get the videos for a token.
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Tests/Ajax/RecruitingToken/SetScreenshotTest
 */
class SetScreenshotTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\RecruitingToken;

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Tests request via ajax endpoint.
     */
    public function testRequest()
    {
        // setup test token
        $RecruitingToken = $this->createRecruitingToken();

        $fileName = rand().'.jpg';

        // test user transfer
        $url = TEST_URL . "/ajax/recruiting_token/set_screenshot";
        $fields = array(
            'tokenId'=>$RecruitingToken->id,
            'fileName'=>$fileName
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
        $this->assertTrue($response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertEquals('true', $return->success);
        $this->assertGreaterThan(0, $return->data->id);

        //check DB was updated
        $image = new RecruitingTokenImage($return->data->id);
        $this->assertEquals($fileName, $image->file_name);
        $this->assertEquals($RecruitingToken->id, $image->recruiting_token_id);

        // cleanup
        $sql = "DELETE FROM recruiting_token_image WHERE id = '{$image->id}'";
        execute_query($sql);
    }

    /**
     * Tests request failure via ajax endpoint.
     */
    public function testFail()
    {
        $url = TEST_URL . "/ajax/recruiting_token/set_screenshot";
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $return = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('', $return);
    }

    /**
     * Tests request failure via ajax endpoint.
     */
    public function testFailAdmin()
    {
        $url = TEST_URL . "/ajax/recruiting_token/set_screenshot";
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_COOKIE, getTestCookie(true));
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertEquals('false', $return->success);
        $this->assertEquals('', $return->data);
    }

    /**
     * Delete things created for testing
     */
    protected function tearDown()
    {
        $this->deleteRecruitingTokens();
    }
}
?>
