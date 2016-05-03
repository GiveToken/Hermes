<?php
namespace Sizzle\Tests;

/**
 * This class tests public/index.php
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/tests/RoutingTest
 */
class RoutingTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the endpoint routing of public/index.php
     */
    public function testPublicEndpoints()
    {
        // test good endpoints not logged in
        $this->assertTrue($this->checkStatusCode(''));
        $this->assertTrue($this->checkStatusCode('/'));
        $this->assertTrue($this->checkStatusCode('/index.html'));
        $this->assertTrue($this->checkStatusCode('/admin', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/active_users', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/city', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/create_account', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/edit_organization', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/organizations', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/no_card_customers', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/send_token', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/stalled_new_customers', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/tokens', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/transfer_token', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/users', false, 302));
        $this->assertTrue($this->checkStatusCode('/admin/visitors', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works/', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works/here', false, 302));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works/here/', false, 302));
        $this->assertTrue($this->checkStatusCode('/invoice', false, 302));
        $this->assertTrue($this->checkStatusCode('/js', false, 301));
        $this->assertTrue($this->checkStatusCode('/js/', false, 404));
        $this->assertTrue($this->checkStatusCode('/organization', false, 302));
        $this->assertTrue($this->checkStatusCode('/robots.txt'));
        $this->assertTrue($this->checkStatusCode('/teapot', false, 418));
        $this->assertTrue($this->checkStatusCode('/tokens', false, 302));
        $this->assertTrue($this->checkStatusCode('/token_responses', false, 302));
        $this->assertTrue($this->checkStatusCode('/upload'));// should be under ajax?
        $this->assertTrue($this->checkStatusCode('/user', false, 302));
        $this->assertTrue($this->checkStatusCode('/test'));// only on DEVELOPMENT
    }

    /**
     * Tests the endpoint routing of public/index.php for logged in admin user
     */
    public function testLoggedInAdminEndpoints()
    {
        $this->assertTrue($this->checkStatusCode('', true, 302, true));
        $this->assertTrue($this->checkStatusCode('/', true, 302, true));
        $this->assertTrue($this->checkStatusCode('/index.html', true, 302, true));
        $this->assertTrue($this->checkStatusCode('/admin', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/active_users', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/city', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/create_account', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/edit_organization', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/organizations', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/no_card_customers', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/send_token', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/stalled_new_customers', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/tokens', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/transfer_token', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/users', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/admin/visitors', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works/', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works/here', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/ajax/anything/at/all/works/here/', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/invoice', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/js', true, 301, true));
        $this->assertTrue($this->checkStatusCode('/js/', true, 404, true));
        $this->assertTrue($this->checkStatusCode('/organization', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/robots.txt', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/teapot', true, 418, true));
        $this->assertTrue($this->checkStatusCode('/tokens', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/token_responses', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/upload', true, 200, true));// should be under ajax?
        $this->assertTrue($this->checkStatusCode('/user', true, 200, true));
        $this->assertTrue($this->checkStatusCode('/test', true, 200, true));// only on DEVELOPMENT
    }

    /**
     * Tests the endpoint routing of public/index.php for logged in admin user
     */
    public function testBadEndpoints()
    {
        // test bad endpoints
        $this->assertTrue($this->checkStatusCode('/broken', false, 404));
        $this->assertTrue($this->checkStatusCode('/admin/broken', false, 404));
    }

    /******************************************************************

              HELPER METHODS

    *******************************************************************/

    /**
     * Checks that a given endpoint returns the desired status code.
     *
     * @param string  $endpoint   - the endpoint to test
     * @param boolean $loggedIn   - is the user logged in (defaults false)
     * @param int     $statusCode - the status code to check for (defaults to 200)
     *
     * @return boolean - desired status code was returned
     */
    private function checkStatusCode(string $endpoint, bool $loggedIn = false, int $statusCode = 200)
    {
        $url = TEST_URL . $endpoint;
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        if ($loggedIn) {
            curl_setopt($ch, CURLOPT_COOKIE, TEST_COOKIE);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $page = ob_get_contents();
        ob_end_clean();
        $foundCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($statusCode == $foundCode) {
            return true;
        } else {
            echo "\nFound status $foundCode instead of $statusCode at '$endpoint'.\n";
            return false;
        }
    }
}
