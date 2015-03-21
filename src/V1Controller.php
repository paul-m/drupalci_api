<?php

namespace API;

use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use API\Entities\Job;

/**
 * Controller for Version 1 of the DrupalCI API.
 */

class V1Controller extends APIController {

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
    $em = $app['orm.em'];
    $job = $em->find('\API\Entities\Job', $id);
    if ($job) {
      $response = $app->json($job, 200);
      return $response;
    }
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
    try {
      $job = Job::createFromRequest($request);
    }
    // @todo Make a better exception type.
    catch (\Exception $e) {
      return new Response('Bad request.', 400);
    }

    $em = $app['orm.em'];
    $em->persist($job);
    $em->flush();

    // Let the request begin.
    // @todo Jenkins should be a service:
    // $jenkins = $app['jenkins'];
    $return = 'fixture'; // BS value while we get Jenkins happening.
/*
    $jenkins = new Jenkins();
    $jenkins->setHost($app['config']['jenkins']['host']);
    $jenkins->setToken($app['config']['jenkins']['token']);
    $jenkins->setBuild($app['config']['jenkins']['job']);
    $jenkins->setQuery($query);
    $return = NULL;
    @$return = $jenkins->send();
*/

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
    // @todo http://silex.sensiolabs.org/doc/providers/security.html
    return new Response("Not supported.", 501);
  }

}
