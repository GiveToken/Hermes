<?php
namespace Sizzle\Tests\Ajax\User;

/**
 * This class tests the ajax endpoint to get the recruiter profile for a token.
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Tests/Ajax/User/GetRecruiterProfileTest
 */
class GetRecruiterProfileTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\Organization, \Sizzle\Bacon\Tests\Traits\RecruitingToken {
        \Sizzle\Bacon\Tests\Traits\Organization::createOrganization insteadof \Sizzle\Bacon\Tests\Traits\RecruitingToken;
        \Sizzle\Bacon\Tests\Traits\Organization::deleteOrganizations insteadof \Sizzle\Bacon\Tests\Traits\RecruitingToken;
    }

    /**
     * Requires the util.php file of functions
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__.'/../../../../util.php';
    }

    /**
     * Set up for the tests by creating User/Company/Token
     */
    public function setUp()
    {
        $this->url = TEST_URL .'/ajax/user/get_recruiter_profile';

        // setup test organization
        $this->Organization = $this->createOrganization();

        // setup test user
        $this->User = $this->createUser();
        $this->User->organization_id = $this->Organization->id;
        $this->User->position = rand();
        $this->User->linkedin = 'https://linkedin.com/in/'.rand();
        $this->User->about = rand();
        $this->User->face_image = rand().'.jpg';
        $this->User->save();

        // setup test token
        $this->RecruitingToken = $this->createRecruitingToken($this->User->id);
    }

    /**
     * Tests request via ajax endpoint.
     */
    public function testRequest()
    {
        // test for created images
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $this->url.'/'.$this->User->id);
        $response = curl_exec($ch);
        $this->assertTrue($response);
        $json = ob_get_contents();
        ob_end_clean();
        $return = json_decode($json);
        $this->assertEquals('true', $return->success);
        $this->assertEquals($this->User->first_name, $return->data->first_name);
        $this->assertEquals($this->User->last_name, $return->data->last_name);
        $this->assertEquals($this->User->position, $return->data->position);
        $this->assertEquals($this->User->linkedin, $return->data->linkedin);
        $this->assertEquals($this->User->about, $return->data->about);
        $this->assertEquals($this->User->face_image, $return->data->face_image);
        $this->assertEquals($this->Organization->website, $return->data->website);
        $this->assertEquals($this->Organization->name, $return->data->organization);
    }

    /**
     * Tests request failure via ajax endpoint.
     */
    public function testFail()
    {
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        $response = curl_exec($ch);
        $this->assertEquals(true, $response);
        $json = ob_get_contents();
        $return = json_decode($json);
        $this->assertEquals('false', $return->success);
        $this->assertEquals('', $return->data);
        ob_end_clean();
    }

    /**
     * Delete users created for testing
     */
    protected function tearDown()
    {
        $this->deleteRecruitingTokens();
        $this->deleteOrganizations();
    }
}
?>
