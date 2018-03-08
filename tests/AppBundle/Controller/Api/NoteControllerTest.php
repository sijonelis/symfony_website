<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-13
 * Time: 20:33
 */

namespace ApiBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class NoteControllerTest extends WebTestCase
{
    public function testNoteWrite()
    {
        $client = static::createClient();
        $headers = ['HTTP_Accept' => 'application/yipiao.api+json;version=1'];

        $goodContent = '{"note": "note text", "tea": {"id": 1}, "notify_staff": true}';
        $badContent = '{"note": "note text", "tea": {"id": 10}, "notify_staff": true}';
        $emptyNote = '{"note": "", "tea": {"id": 1}, "notify_staff": false}';

        //test accept header
        $crawler = $client->request('POST', '/api/note/write', [], [], [], $goodContent);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        //test bad content type (should pass auth test and return 415 error)
        $crawler = $client->request('POST', '/api/note/write', [], [], $headers);
        $this->assertEquals(415, $client->getResponse()->getStatusCode());

        $headers['CONTENT_TYPE'] = 'application/json';

        //test bad auth header
        $crawler = $client->request('POST', '/api/note/write', [], [], $headers, $goodContent);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertContains('Access token is missing!', $client->getResponse()->getContent());

        //test existing tea (good content, headers test should auto pass)
        $headers['HTTP_Authorization'] = 'Bearer {123456}';
        $crawler = $client->request('POST', '/api/note/write', [], [], $headers, $goodContent);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        //test nonexisting tea (bad content)
        $crawler = $client->request('POST', '/api/note/write', [], [], $headers, $badContent);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        //test empty note
        $crawler = $client->request('POST', '/api/note/write', [], [], $headers, $emptyNote);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());


    }
}