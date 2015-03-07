<?php

namespace API\Tests;

use API\Jenkins;

/**
 * Class JenkinsTest.
 * Test the functionality of the Jenkins class.
 *
 * @coversDefaultClass \API\Jenkins
 */
class JenkinsTest extends \PHPUnit_Framework_TestCase {

  /**
   * Build a Jenkins object and get the URL that will be used for submission.
   *
   * @covers ::sendRequest
   */
  public function testSendRequest() {
    // Create a mock guzzle client.
    $mock_guzzle = $this->getMockBuilder('GuzzleHttp\Client')
      ->disableOriginalConstructor()
      ->setMethods(array('get'))
      ->getMock();
    // Set the mock for the get() method.
    $mock_guzzle->expects($this->once())
      ->method('get')
      ->willReturnCallback(function ($url, $params) {
        return [$url, $params];
      });

    $jenkins = new Jenkins($mock_guzzle);
    $jenkins->setProtocol('https');
    $jenkins->setHost('localhost');
    $jenkins->setPort('9090');
    $jenkins->setBuild('foo');
    $jenkins->setToken('99999999');
    $jenkins->setQuery(array(
      'repository' => 'baz',
      'branch' => 'bar',
      'patch' => 'bas'
    ));
    $request = $jenkins->sendRequest();

    // Check a successful request.
    $expected = [
      'https://localhost:9090/job/foo/buildWithParameters',
      [
        'query' => [
          'token' => '99999999',
          'repository' => 'baz',
          'branch' => 'bar',
          'patch' => 'bas',
        ],
        'verify' => FALSE,
      ]
    ];
    $this->assertEquals($expected, $request);

    // Check a successful return message.
    // @todo Move this to another test.
//    $message = $jenkins->send();
//    $this->assertEquals('The message has been sent to the dispatcher.', $message);
  }

}
