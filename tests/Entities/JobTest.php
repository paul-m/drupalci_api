<?php

namespace API\Tests\Entities;

use API\Entities\Job;

/**
 * @coversDefaultClass \API\Entities\Job
 */
class JobTest extends \PHPUnit_Framework_TestCase {
  public function testJob() {
    $job = new Job();
    $this->assertNotNull($job);
  }
}
