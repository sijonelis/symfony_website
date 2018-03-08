<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-01
 * Time: 21:05
 */

namespace ApiBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class TeaControllerTest extends WebTestCase
{
    public function testTeaGet()
    {
        $client = static::createClient();
        $headers = ['HTTP_Accept' => 'application/yipiao.api+json;version=1'];
        $headers['HTTP_Authorization'] = 'Bearer {123456}';

        //test accept header
        $crawler = $client->request('GET', '/api/tea/get/1', [], [], []);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        //test existing tea
        $crawler = $client->request('GET', '/api/tea/get/1', [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('logged_in', $client->getResponse()->getContent());

        //test nonexisting tea
        $crawler = $client->request('GET', '/api/tea/get/5', [], [], $headers);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Tea does not exist', $client->getResponse()->getContent());
    }
}