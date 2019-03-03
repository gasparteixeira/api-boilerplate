<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

/**
 * Description of APIControllerTest
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class APIControllerTest extends WebTestCase {

    protected function setUp() {
        $mock = new MockHandler([new Response(200, ["welcome" => "Your API is working!"])]);
        $handler = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handler]);
    }

    public function testWithMockHandler() {
        $response = $this->client->post("/", null);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getReasonPhrase());
        $this->assertArrayHasKey("welcome", $response->getHeaders());
        $this->assertTrue(in_array("Your API is working!", $response->getHeaders()["welcome"]));
    }

    public function testForRoot() {
        $client = static::createClient([], []);
        $client->request("GET", "/");
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testForUrlInexistent() {
        $client = static::createClient([], []);
        $client->request("POST", "/api");
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testForUserRenewTokenInexistent() {
        $client = static::createClient([], [
                    'PHP_AUTH_USER' => 'john_user',
                    'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->request("POST", "/api/retoken");
        $this->assertSame(401, $client->getResponse()->getStatusCode());
    }

    public function testForUserTokenInexistent() {
        $client = static::createClient([], [
                    'PHP_AUTH_USER' => 'john_user',
                    'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->request("POST", "/api/token");
        $this->assertSame(401, $client->getResponse()->getStatusCode());
    }

    public function testForUserExistent() {
        $client = static::createClient([], [
                    'PHP_AUTH_USER' => 'boilerplate',
                    'PHP_AUTH_PW' => 'S3cr37W0rd',
        ]);
        $client->request("POST", "/api/register");
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testWithAdiferentMethod(string $httpMethod, string $url) {
        $client = static::createClient([], [
                    'PHP_AUTH_USER' => 'boilerplate',
                    'PHP_AUTH_PW' => 'S3cr37W0rd',
        ]);
        $client->request($httpMethod, $url);
        $this->assertSame(405, $client->getResponse()->getStatusCode());
    }

    public function getUrlsForRegularUsers() {
        yield ['GET', '/api/register'];
        yield ['GET', '/api/token'];
        yield ['GET', '/api/retoken'];
        yield ['PUT', '/api/register'];
        yield ['PUT', '/api/token'];
        yield ['PUP', '/api/retoken'];
        yield ['DELETE', '/api/register'];
        yield ['DELETE', '/api/token'];
        yield ['DELETE', '/api/retoken'];
        yield ['OPTIONS', '/api/register'];
        yield ['OPTIONS', '/api/token'];
        yield ['OPTIONS', '/api/retoken'];
        yield ['HEAD', '/api/register'];
        yield ['HEAD', '/api/token'];
        yield ['HEAD', '/api/retoken'];
    }

}
