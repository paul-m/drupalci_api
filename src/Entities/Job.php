<?php

namespace API\Entities;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Entity
 * @Table(name="job")
 */
class Job implements \JsonSerializable {

  /**
   * @Id
   * @Column(type="integer")
   * @GeneratedValue
   */
  protected $id;

  /**
   * @Column(type="date", options={"default" = "1990-01-01"})
   * @var \DateTime
   */
  protected $created;

  /**
   * @Column(type="date", options={"default" = "1990-01-01"})
   * @var \DateTime
   */
  protected $lastCheck;

  /** @Column(type="string", length=255, unique=false, nullable=false) */
  protected $repository;

  /** @Column(type="string", length=255, unique=false, nullable=false) */
  protected $branch;

  /** @Column(type="string", length=255, unique=false, nullable=false) */
  protected $patch;

  /** @Column(type="string", length=255, unique=false, nullable=true) */
  protected $status;

  /** @Column(type="string", length=255, unique=false, nullable=true) */
  protected $result;

  /** @Column(type="string", length=255, unique=false, nullable=true) */
  protected $jenkinsUri;

  /**
   * Store log information for job progress, so it can be shown and updated
   * through API requests.
   * @todo: Decide if this is a good idea.
   * @Column(type="text", nullable=true)
   */
  protected $log;

  /**
   * Construct a Job object.
   */
  public function __construct() {
    // We must put a DateTime object in some properties or Doctrine will
    // complain.
    $this->created = new \DateTime();
    $this->lastCheck = new \DateTime();
  }

  public static function createFromRequest(Request $request) {
    $query = [];
    foreach (['repository', 'branch', 'patch'] as $query_key) {
      $query[$query_key] = $request->get($query_key, '');
    }
    // Sanity check.
    if (empty($query['repository']) || empty($query['branch'])) {
      // @todo: Make a meaningful exception class.
      throw new \Exception('Job start requests need at least a repository and a branch.');
    }
    $job = new Job();
    $job->setBranch($query['branch']);
    $job->setPatch($query['patch']);
    $job->setRepository($query['repository']);
    return $job;
  }

  /**
   * Add a line to the log.
   */
  public function log($message) {
    // @todo: figure out sanitizing doctrine ORM fields.
    // @todo: figure out if we even want this.
    $this->log .= "\n" . $message;
    return $this;
  }

  /**
   * Check if this record is old enough to expire and re-request from Jenkins.
   *
   * @param int $seconds
   *
   * @return bool
   */
  public function isOlderThan($seconds) {
    return FALSE;
  }

  /**
   * @return \DateTime
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set repository
   *
   * @param string $repository
   * @return Job
   */
  public function setRepository($repository) {
    $this->repository = $repository;

    return $this;
  }

  /**
   * Get repository
   *
   * @return string
   */
  public function getRepository() {
    return $this->repository;
  }

  /**
   * Set branch
   *
   * @param string $branch
   * @return Job
   */
  public function setBranch($branch) {
    $this->branch = $branch;

    return $this;
  }

  /**
   * Get branch
   *
   * @return string
   */
  public function getBranch() {
    return $this->branch;
  }

  /**
   * Set patch
   *
   * @param string $patch
   * @return Job
   */
  public function setPatch($patch) {
    $this->patch = $patch;

    return $this;
  }

  /**
   * Get patch
   *
   * @return string
   */
  public function getPatch() {
    return $this->patch;
  }

  /**
   * Set status
   *
   * @param string $status
   * @return Job
   */
  public function setStatus($status) {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   *
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Set result
   *
   * @param string $result
   * @return Job
   */
  public function setResult($result) {
    $this->result = $result;

    return $this;
  }

  /**
   * Get result
   *
   * @return string
   */
  public function getResult() {
    return $this->result;
  }

  /**
   * Set Jenkins URI
   *
   * @param string $result
   * @return Job
   */
  public function setJenkinsUri($jenkinsUri) {
    $this->jenkinsUri = $jenkinsUri;

    return $this;
  }

  /**
   * Get Jenkins URI
   *
   * @return string
   */
  public function getJenkinsUri() {
    return $this->jenkinsUri;
  }

  /**
   * Set log
   *
   * @param string $log
   * @return Job
   */
  public function setLog($log) {
    $this->log = $log;

    return $this;
  }

  /**
   * Get log
   *
   * @return string
   */
  public function getLog() {
    return $this->log;
  }

  public function jsonSerialize() {
    $result = new \stdClass();
    $properties = [
      'id',
      'repository',
      'branch',
      'patch',
      'status',
      'result',
      'log',
      'jenkinsUri'
    ];
    foreach ($properties as $property) {
      $result->$property = $this->$property;
    }
    return $result;
  }

}
