<?php

namespace API;

use Silex\Application;

/**
 * @file
 * Interface for DrupalCI API.
 */

interface APIInterface {

  /**
   * Information on how to use the API.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function home();

  /**
   * Runs a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobRun(Application $app);

  /**
   * Get the status of a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobStatus(Application $app, $id);

  /**
   * Cancel a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobCancel(Application $app, $id);

  /**
   * Restarts a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobRestart(Application $app, $id);

  /**
   * Gets a jobs console output from the dispatcher.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobConsole(Application $app, $id);

  /**
   * Get the results of the build.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobResults(Application $app, $id);

  /**
   * Authenticate against the API.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function auth(Application $app, $token);

  /**
   * Get global API status.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function status(Application $app);

}
