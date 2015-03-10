<?php

namespace API\Tests\Fixtures;

use API\Entities\Job;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

class APIFixture implements FixtureInterface {

  public function load(ObjectManager $manager) {
    $job = new Job();
    $job->setBranch('test_branch')
      ->setLog('test_log')
      ->setPatch('test_patch')
      ->setRepository('test_repository')
      ->setResult('test_result')
      ->setStatus('test_status');

    $manager->persist($job);
    $manager->flush();
  }

}
