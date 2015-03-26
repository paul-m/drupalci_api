<?php

namespace API\Services;

use Silex\Application;
use API\Services\Jenkins;
use API\Services\Results;
use Doctrine\ORM\EntityManager;
use API\Entities\Job;

class Runner {

  protected $em;
  protected $jenkins;
  protected $results;
  protected $job;

  public function __construct(EntityManager $em, Jenkins $jenkins, Results $results) {
    $this->em = $em;
    $this->jenkins = $jenkins;
    $this->results = $results;
  }

  public static function create(Application $app) {
    return new static(
      $app['orm.em'],
      $app['jenkins'],
      $app['results']
    );
  }

  public function setJob(Job $job) {
    $this->job = $job;
    return $this;
  }

  public function sendToJenkins($token) {
    $this->jenkins->setToken($token);
    $this->jenkins->setBuild($this->job->getId());
    return $this->jenkins->send();
  }

  public function sendToResults() {
    $this->results->ping();
  }

}
