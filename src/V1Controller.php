<?php

namespace API;

use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * Controller for Version 1 of the DrupalCI API.
 */

class V1Controller extends APIController {

  private function getJobDefault() {
    return [
      'id' => NULL,
      'created' => 0,
      'repository' => '',
      'branch' => '',
      'patch' => '',
      'status' => NULL,
      'result' => '',
      'log' => '',
    ];
  }

  /**
   * Information on how to use the API.
   * @return message.
   */
  public function home() {
    return new Response("Welcome to the DrupalCI API.");
  }

  /**
   * Gather status information about a Jenkins build.
   *
   * @param Application $app
   * @param mixed $id
   */
  public function jobStatus(Application $app, $id) {
    // Currently we can't return any information.
    $job = [
      'status' => 404,
      'message' => 'Record could not be found.',
    ];
    $response = $app->json($job, 404);
    return $response;
  }

  /**
   * Runs a job.
   * @return id.
   */
  public function jobRun(Application $app) {
    // The request we're working on.
    $request = $app['request'];
    // Get our params.
    $query = [
      'repository' => $request->get('repository', ''),
      'branch' => $request->get('branch', ''),
      'patch' => $request->get('patch', ''),
    ];
    // Parameter check.
    if (empty($query['repository']) || empty($query['branch'])) {
      return new Response('Job start requests need at least a repository and a branch.', 400);
    }

    // Let the request begin.
    // Jenkins should be a service:
    // $jenkins = $app['jenkins'];
    $jenkins = new Jenkins();
    $jenkins->setHost($app['config']['jenkins']['host']);
    $jenkins->setToken($app['config']['jenkins']['token']);
    $jenkins->setBuild($app['config']['jenkins']['job']);
    $jenkins->setQuery($query);
    $return = NULL;
    @$return = $jenkins->send();

    // Check the return to make sure we had a successful submission.
    if (empty($return)) {
      return new Response("Jenkins build was not successful.", 504);
    }
    else {
      return new Response('The build is in the queue at the following address: ' . $return, 200);
    }
    // Default behavior if nothing else works out.
    $response = new Response('Unable to run job.', 501);
  }

  /**
   * Authenticate against the API.
   * @return success.
   */
  public function auth(Application $app, $token) {
    // http://silex.sensiolabs.org/doc/providers/security.html
    return new Response("Not supported.", 501);
  }

}
