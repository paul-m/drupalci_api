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
    // @todo: Ping Jenkins to find out an update.
    $response = $app->json($job, 404);
    return $response;
  }

  /**
   * Runs a job.
   * @return id.
   */
  public function jobRun(Application $app) {
    // Default behavior if nothing else works out.
    $message = 'Unable to run job.';
    $response = new Response($message, 501);

    // The request we're working on.
    $request = $app['request'];
    try {
      $job = Job::createFromRequest($request);
    }
    // @todo Make a better exception type.
    catch (\Exception $e) {
      return new Response('Bad request.', 400);
    }

    // We have to persist our Job entity in order to generate an ID.
    $em = $app['orm.em'];
    $em->persist($job);
    $em->flush();

    $jenkins = $app['jenkins'];
    $jenkins->setToken($app['config']['jenkins']['token']);
    $jenkins->setBuild($job->getId());
    $result = $jenkins->send();

    // Check the return to make sure we had a successful submission.
    if ($result === FALSE) {
      $message = 'Jenkins build was not successful.';
      $job->setResult('error');
      $job->setStatus('error');
      $response = new Response($message, 504);
    }
    else {
      // @todo: Make this json, hateoas, etc.
      $message = 'The build is in the queue at the following address: ' . $result;
      $job->setStatus('building');
      $response = new Response($message, 200);
    }
    $job->log($message);
    $em->persist($job);
    $em->flush();
    return $response;
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
