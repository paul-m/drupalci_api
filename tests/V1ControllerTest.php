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
    $this->assertEquals(404, $response->getStatusCode());
    $json = json_decode($response->getContent());
    $this->assertEquals(404, $json->status);
    $this->assertNotEmpty($json->message);
  }

  public function testJobStatus() {
    $client = $this->createClient();
    $crawler = $client->request('GET', $this->apiPrefix() . '/job/1');
    $response = $client->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(
      '{"id":1,"repository":"test_repository","branch":"test_branch","patch":"test_patch","status":"test_status","result":"test_result","log":"test_log"}',
      $response->getContent()
    );
  }

  public function testJobStatusJsonp() {
    $client = $this->createClient();
    $crawler = $client->request('GET', $this->apiPrefix() . '/job/1', ['callback' => 'jsonp']);
    $response = $client->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(
      '/**/jsonp({"id":1,"repository":"test_repository","branch":"test_branch","patch":"test_patch","status":"test_status","result":"test_result","log":"test_log"});',
      $response->getContent()
    );
  }

  /**
   * Posting a job without any repo/branch/patch content.
   */
  public function testJobRun400() {
    $client = $this->createClient();
    $crawler = $client->request(
      'POST', $this->apiPrefix() . '/job',
      [],
      [],
      array('CONTENT_TYPE' => 'application/json'),
      '' // Empty content.
    );
    $response = $client->getResponse();

    $this->assertEquals(400, $response->getStatusCode());
  }

  /**
   * A reasonable expectation that we'd be able to generate a job run.
   */
  public function testJobRun() {
    // Mock up Guzzle's response message.
    $mock_response = $this->getMockBuilder('\GuzzleHttp\Message\MessageInterface')
      ->setMethods(['getHeader'])
      ->getMockForAbstractClass();
    $mock_response->expects($this->once())
      ->method('getHeader')
      ->willReturn('not our default url');
    // Mock Guzzle.
    $mock_client = $this->getMockBuilder('\GuzzleHttp\Client')
      ->setMethods(['get'])
      ->getMock();
    $mock_client->expects($this->once())
      ->method('get')
      ->willReturn($mock_response);

    // Set our Jenkins service to use the mocked Guzzle.
    $jenkins = $this->app['jenkins'];
    $jenkins->setClient($mock_client);

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
  }

}
