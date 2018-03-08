<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-14
 * Time: 22:53
 */

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class ProfileControllerTest extends WebTestCase
{

    public function testProfileGet()
    {
        $method = 'GET';
        $uri = '/api/profile/get';

        $client = static::createClient();
        $headers = ['HTTP_Accept' => 'application/yipiao.api+json;version=1'];
//        $headers['HTTP_Authorization'] = 'Bearer {123456}';

        //test accept header
        $crawler = $client->request($method, $uri, [], [], []);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        //test bad auth header
        $crawler = $client->request($method, $uri, [], [], $headers);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertContains('errors', $client->getResponse()->getContent());
        $this->assertContains('Access token is missing!', $client->getResponse()->getContent());

        //test goot auth header
        $headers['HTTP_Authorization'] = 'Bearer {123456}';
        $crawler = $client->request($method, $uri, [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('"nickname":"test_user"', $client->getResponse()->getContent());
    }

}