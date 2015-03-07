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
    $crawler = $client->request('GET', $this->apiPrefix() . '/job/status/1');
    $response = $client->getResponse();
    $json = json_decode($response->getContent());

    $this->assertEquals(404, $response->getStatusCode());
    $this->assertEquals(404, $json->status);
    $this->assertNotEmpty($json->message);
  }

  /**
   * Starting a job without a fixture should result in 404.
   */
  public function testJobRun() {
    $client = $this->createClient();
    $crawler = $client->request('GET', $this->apiPrefix() . '/job/status/1');
    $response = $client->getResponse();
    $json = json_decode($response->getContent());

    $this->assertEquals(404, $response->getStatusCode());
    $this->assertEquals(404, $json->status);
    $this->assertNotEmpty($json->message);
  }

}
