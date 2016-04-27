<?php
namespace Sizzle\Tests\Ajax\RecruitingToken;

use \Sizzle\Bacon\Database\{
    RecruitingCompanyVideo
};

/**
 * This class tests the ajax endpoint to get the videos for a token.
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Tests/Ajax/RecruitingToken/GetVideosTest
 */
class GetVideosTest
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
        $RecruitingCompanyVideo = new RecruitingCompanyVideo();
        $source = 'vimeo';
        $source_id[1] = rand();
        $source_id[2] = rand();
        $source_id[3] = rand();
        $id[1] = $RecruitingCompanyVideo->create($recruitingToken->recruiting_company_id, $source, $source_id[1]);
        $id[2] = $RecruitingCompanyVideo->create($recruitingToken->recruiting_company_id, $source, $source_id[2]);
        $id[3] = $RecruitingCompanyVideo->create($recruitingToken->recruiting_company_id, $source, $source_id[3]);
        $this->videos = $id;

        // test for created images
        $url = TEST_URL . "/ajax/recruiting_token/get_videos/{$recruitingToken->long_id}";
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
            $this->assertTrue(in_array($image->source_id, $source_id));
        }
    }

    /**
     * Tests request failure via ajax endpoint.
     */
    public function testFail()
    {
        $url = TEST_URL . "/ajax/recruiting_token/get_videos";
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
        if (isset($this->videos)) {
            foreach ($this->videos as $id) {
                $sql = "DELETE FROM recruiting_company_video WHERE id = '$id'";
                execute_query($sql);
            }
        }
        $this->deleteRecruitingTokens();
    }
}
?>
