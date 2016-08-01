<?php
namespace Sizzle\Tests\Ajax\RecruitingToken;

use Sizzle\Bacon\Database\RecruitingToken;

/**
 * This class tests the ajax endpoint to transfer tokens between users.
 *
 * ./vendor/bin/phpunit --bootstrap src/Tests/autoload.php src/Tests/Ajax/RecruitingToken/TransferTest
 */
class TransferTest
extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\RecruitingToken,
        \Sizzle\Bacon\Tests\Traits\User {
            \Sizzle\Bacon\Tests\Traits\User::createUser insteadof \Sizzle\Bacon\Tests\Traits\RecruitingToken;
            \Sizzle\Bacon\Tests\Traits\User::deleteUsers insteadof \Sizzle\Bacon\Tests\Traits\RecruitingToken;
    }

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
    public function testNoCompany()
    {
        // setup test users
        $User1 = $this->createUser();
        $User2 = $this->createUser();

        // setup test token
        $RecruitingToken = $this->createRecruitingToken($User1->id, 'none');

        // test user transfer
        $url = TEST_URL . "/ajax/recruiting_token/transfer";
        $fields = array(
            'token_id'=>$RecruitingToken->long_id,
            'old_user_id'=>$User1->id,
            'new_user_id'=>$User2->id
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

        //check DB was updated
        $RecruitingToken2 = new RecruitingToken($RecruitingToken->long_id, 'long_id');
        $this->assertEquals($User2->id, $RecruitingToken2->user_id);
    }

    /**
     * Tests request via ajax endpoint.
     */
    public function testOldUserCompany()
    {
        // setup test users
        $User1 = $this->createUser();
        $User2 = $this->createUser();

        // setup test company with unrelated user
        $RecruitingCompany = $this->createRecruitingCompany();

        // setup test tokens
        $this->createRecruitingToken($User1->id, $RecruitingCompany->id);
        $RecruitingToken = $this->createRecruitingToken($User1->id, $RecruitingCompany->id);

        // test user transfer
        $url = TEST_URL . "/ajax/recruiting_token/transfer";
        $fields = array(
            'token_id'=>$RecruitingToken->long_id,
            'old_user_id'=>$User1->id,
            'new_user_id'=>$User2->id
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
        $this->assertEquals('false', $return->success);
        $this->assertEquals("This company ({$RecruitingCompany->name}) belongs to a different organization.", $return->data->error);

        //check DB was not updated
        $RecruitingToken2 = new RecruitingToken($RecruitingToken->long_id, 'long_id');
        $this->assertEquals($User1->id, $RecruitingToken2->user_id);
    }

    /**
     * Tests request via ajax endpoint.
     */
    public function testNewUserCompany()
    {
        // setup test users
        $User1 = $this->createUser();
        $User2 = $this->createUser();
        $org2 = $this->createOrganization();
        $User2->organization_id = $org2->id;
        $User2->save();

        // setup test company
        $RecruitingCompany = $this->createRecruitingCompany($User2->organization_id);

        // setup test token
        $RecruitingToken = $this->createRecruitingToken($User1->id, $RecruitingCompany->id);

        // test user transfer
        $url = TEST_URL . "/ajax/recruiting_token/transfer";
        $fields = array(
            'token_id'=>$RecruitingToken->long_id,
            'old_user_id'=>$User1->id,
            'new_user_id'=>$User2->id
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

        //check DB was updated
        $RecruitingToken2 = new RecruitingToken($RecruitingToken->long_id, 'long_id');
        $this->assertEquals($User2->id, $RecruitingToken2->user_id);
    }

    /**
     * Tests request failure via ajax endpoint.
     */
    public function testFail()
    {
        $url = TEST_URL . "/ajax/recruiting_token/transfer";
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
        $this->deleteRecruitingTokens();
    }
}
?>
