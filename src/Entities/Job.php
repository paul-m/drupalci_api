<?php

namespace API\Entities;

/**
 * @Entity
 * @Table(name="job")
 */
class Job {

  /**
   * @Id
   * @Column(type="integer")
   * @GeneratedValue
   */
  protected $id;

  /** @Co_lumn(type="datetime", nullable=false) */
  protected $created;

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

  /** @Column(type="text", nullable=true) */
  protected $log;

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

}
