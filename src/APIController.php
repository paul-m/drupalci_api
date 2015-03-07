<?php

namespace API;

use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * @file
 * A base controller class that we can extend from for future API.
 */

/**
 * Base class for implementing our API interface.
 */
abstract class APIController implements APIInterface {

  /**
   * Information on how to use the API.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function home() {
    return new Response("Not supported.", 501);
  }

  /**
   * Runs a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobRun(Application $app) {
    return new Response("Not supported.", 501);
  }

  /**
   * Get the status of a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobStatus(Application $app, $id) {
    return new Response("Not supported.", 501);
  }

  /**
   * Cancel a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobCancel(Application $app, $id) {
    return new Response("Not supported.", 501);
  }

  /**
   * Restarts a job.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobRestart(Application $app, $id) {
    return new Response("Not supported.", 501);
  }

  /**
   * Gets a jobs console output from the dispatcher.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobConsole(Application $app, $id) {
    return new Response("Not supported.", 501);
  }

  /**
   * Get the results of the build.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function jobResults(Application $app, $id) {
    return new Response("Not supported.", 501);
  }

  /**
   * Authenticate against the API.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function auth(Application $app, $token) {
    return new Response("Not supported.", 501);
  }

  /**
   * Get global API status.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function status(Application $app) {
    return new Response("Not supported.", 501);
  }

}
