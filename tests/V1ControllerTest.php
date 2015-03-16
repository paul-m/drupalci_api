<?php

/**
 * @file
 * Make sure V1Controller minimally works.
 */

namespace API\Tests;

use API\Tests\Api1TestBase;
use Symfony\Component\BrowserKit\Client;

class V1ControllerTest extends Api1TestBase {

  /**
   * Getting a status without a fixture should result in 404.
   */
  public function testJobStatus404() {
    $client = $this->createClient();
    $crawler = $client->request('GET', $this->apiPrefix() . '/job/0');
    $response = $client->getResponse();
    $json = json_decode($response->getContent());

    $this->assertEquals(404, $response->getStatusCode());
    $this->assertEquals(404, $json->status);
    $this->assertNotEmpty($json->message);
  }

  public function testJobStatus() {
    $client = $this->createClient();
    $crawler = $client->request('GET', $this->apiPrefix() . '/job/1');
    $response = $client->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(
      '{"id":"1","repository":"test_repository","branch":"test_branch","patch":"test_patch","status":"test_status","result":"test_result","log":"test_log"}', $response->getContent()
    );
  }

  public function testJobRun() {
    $client = $this->createClient();
    $crawler = $client->request(
      'POST', $this->apiPrefix() . '/job',
      [],
      [],
      array('CONTENT_TYPE' => 'application/json'),
      '{"repository":"r","branch":"b", "patch":"p"}'
    );
    $response = $client->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('', $response->getContent());
//    $this->assertEquals(404, $json->status);
    //  $this->assertNotEmpty($json->message);
  }

}
