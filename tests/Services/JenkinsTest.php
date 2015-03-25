<?php

namespace API\Tests\Services;

use API\Services\Jenkins;
use API\Entities\Job;

/**
 * Class JenkinsTest.
 * Test the functionality of the Jenkins class.
 *
 * @coversDefaultClass \API\Jenkins
 */
class JenkinsTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers ::send
   */
  public function testSend() {
    $expected = 'does not contain our original url';

    $mock_response = $this->getMockBuilder('\GuzzleHttp\Message\MessageInterface')
      ->setMethods(['getHeader'])
      ->getMockForAbstractClass();
    $mock_response->expects($this->once())
      ->method('getHeader')
      ->willReturn($expected);

    $mock_client = $this->getMockBuilder('\GuzzleHttp\Client')
      ->setMethods(['get'])
      ->getMock();
    $mock_client->expects($this->once())
      ->method('get')
      ->willReturn($mock_response);

    $jenkins = new Jenkins();
    $jenkins->setClient($mock_client);
    $jenkins->setBuild('test_build');
    $jenkins->setToken('test_token');
    $this->assertEquals($expected, $jenkins->send());
  }

}
