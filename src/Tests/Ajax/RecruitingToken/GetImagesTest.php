<?php
namespace Sizzle\Tests\Ajax\RecruitingToken;

use \Sizzle\Bacon\Database\{
    RecruitingCompanyImage
};

/**
 * This class tests the ajax endpoint to get the images for a token.
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Tests/Ajax/RecruitingToken/GetImagesTest
 */
class GetImagesTest
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
        $recruitingToken = $this->createRecruitingToken();

        // setup test images
        $RecruitingCompanyImage = new RecruitingCompanyImage();
        $file_name[1] = rand().'.jpg';
        $file_name[2] = rand().'.jpg';
        $file_name[3] = rand().'.jpg';
        $id[1] = $RecruitingCompanyImage->create($recruitingToken->recruiting_company_id, $file_name[1]);
        $id[2] = $RecruitingCompanyImage->create($recruitingToken->recruiting_company_id, $file_name[2]);
        $id[3] = $RecruitingCompanyImage->create($recruitingToken->recruiting_company_id, $file_name[3]);
        $this->images = $id;

        // test for created images
        $url = TEST_URL . "/ajax/recruiting_token/get_images/{$recruitingToken->long_id}";
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $this->assertTrue($response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertEquals('true', $return->success);
        //print_r($return);
        foreach ($return->data as $image) {
            $this->assertTrue(in_array($image->id, $id));
            $this->assertTrue(in_array($image->file_name, $file_name));
        }
    }

    /**
     * Tests request failure via ajax endpoint.
     */
    public function testFail()
    {
        $url = TEST_URL . "/ajax/recruiting_token/get_images";
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
     * Delete things created for testing
     */
    protected function tearDown()
    {
        if (isset($this->images)) {
            foreach ($this->images as $id) {
                $sql = "DELETE FROM recruiting_company_image WHERE id = '$id'";
                execute_query($sql);
            }
        }
        $this->deleteRecruitingTokens();
    }
}
?>
