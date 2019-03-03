<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of APIControllerTest
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class APIControllerTest extends WebTestCase {

    public function testForRoot() {
        $client = static::createClient([], []);
        $client->request("GET", "/");
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testForUrlInexistent() {
        $client = static::createClient([], []);
        $client->request("POST", "/api");
        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testForUserRenewTokenInexistent() {
        $client = static::createClient([], [
                    'PHP_AUTH_USER' => 'john_user',
                    'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->request("POST", "/api/retoken");
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testForUserTokenInexistent() {
        $client = static::createClient([], [
                    'PHP_AUTH_USER' => 'john_user',
                    'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->request("POST", "/api/token");
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testForUserExistent() {
        $client = static::createClient([], [
                    'PHP_AUTH_USER' => 'boilerplate',
                    'PHP_AUTH_PW' => 'S3cr37W0rd',
        ]);
        $client->request("POST", "/api/register");
        $this->assertSame(REsponse::HTTP_OK, $client->getResponse()->getStatusCode());
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
        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
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
