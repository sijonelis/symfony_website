<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-10-01
 * Time: 13:07
 */

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class EmailLoginControllerTest extends WebTestCase
{
    public function testEmailUserCreate() {
        $client = static::createClient();
            $noAcceptHeader = [
                'HTTP_App_Version' => 123,
                'HTTP_Device_Version' => 123,
                'HTTP_Device_Id' => 'And-123'
            ];
        $headers = [
            'HTTP_Accept' => 'application/yipiao.api+json;version=1',
            'HTTP_APP_VERSION' => 123,
            'HTTP_DEVICE_VERSION' => 123,
            'HTTP_DEVICE_ID' => 'And-123'
        ];
//        $headers = [
//            'HTTP_Accept' => 'application/yipiao.api+json;version=1',
//            'HTTP_App-Version' => 123,
//            'HTTP_Device-Version' => 123,
//            'HTTP_Device-Id' => 'And-123'
//        ];

        $goodContent = '{"email": "test_user' . rand(10000, 999999) . '@test.com", "password": "secretPassword"}';
        $existingUser = '{"email": "test_user@test.com", "password": "password"}';
        $badContent1 = '{"password": "secretPassword"}';
        $badContent2 = '{"email": "testuser@test.com"}';

        //test no accept header
        $crawler = $client->request('POST', '/api/email/register', [], [], $noAcceptHeader, $goodContent);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

//        //test bad content type (should pass auth test and return 415 error)
//        $crawler = $client->request('POST', '/api/email/register', [], [], $headers);
//        $this->assertEquals(415, $client->getResponse()->getStatusCode());

        $headers['CONTENT_TYPE'] = 'application/json';

        $client->setServerParameters($headers);
        //test bad requests (headers test should auto pass)
        $crawler = $client->request('POST', '/api/email/register', [], [], $headers, $badContent1);
        print_r($client->getResponse()->getContent());
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        //test bad requests (headers test should auto pass)
        $crawler = $client->request('POST', '/api/email/register', [], [], $headers, $badContent2);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        //create a good user
        $crawler = $client->request('POST', '/api/email/register', [], [], $headers, $goodContent);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //try to create existing user
        $crawler = $client->request('POST', '/api/email/register', [], [], $headers, $existingUser);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testEmailUserLogin() {
        $client = static::createClient();
        $headers = ['HTTP_Accept' => 'application/yipiao.api+json;version=1'];

        $existingUser = '{"email": "test_user@test.com", "password": "password"}';
        $existingBadPasswordUser = '{"email": "test_user@test.com", "password": "badPassword"}';
        $badContent1 = '{"password": "secretPassword"}';
        $badContent2 = '{"email": "testuser@test.com"}';

        //test accept header
        $crawler = $client->request('POST', '/api/email/login', [], [], [], $existingUser);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

//        //test bad content type (should pass auth test and return 415 error)
//        $crawler = $client->request('POST', '/api/email/register', [], [], $headers);
//        $this->assertEquals(415, $client->getResponse()->getStatusCode());

        $headers['CONTENT_TYPE'] = 'application/json';

        //test bad requests (headers test should auto pass)
        $crawler = $client->request('POST', '/api/email/login', [], [], $headers, $badContent1);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        //test bad requests (headers test should auto pass)
        $crawler = $client->request('POST', '/api/email/login', [], [], $headers, $badContent2);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        //login with user
        $crawler = $client->request('POST', '/api/email/login', [], [], $headers, $existingUser);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //bad password login attempt
        $crawler = $client->request('POST', '/api/email/login', [], [], $headers, $existingBadPasswordUser);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}