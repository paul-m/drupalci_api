<?php

namespace API;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use API\Entities\Job;

/**
 * Controller for Version 1 of the DrupalCI API.
 */

class V1Controller extends APIController {

  /**
   * Information on how to use the API.
   * @todo: Return stats in human-readable form.
   *
   * @return Symfony\Component\HttpFoundation\Response
   */
  public function home() {
    return new Response("Welcome to the DrupalCI API.");
  }

  /**
   * Gather status information about a build.
   *
   * Based on this diagram: https://www.previousnext.com.au/sites/default/files/DrupalCI%20Testbot.png
   * we have to do the following:
   * - Look for the record in our local DB.
   * - If we don't have it, 404.
   * - If it's older than [stale] seconds, GET from Results.
   * - Store the record.
   * - Return the record, 200.
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
   *
   * Based on this diagram: https://www.previousnext.com.au/sites/default/files/DrupalCI%20Testbot.png
   * we have to do the following:
   * - Create a local record for the request.
   * - Use the ID for the local record as the test ID.
   * - Start a Jenkins job.
   * - Send the entity to the Results server.
   * - Log. Mechanism to be determined.
   * - Return record to sender, 200.
   *
   * @return id.
   */
  public function jobRun(Application $app) {
    // Default behavior if nothing else works out.
    $message = 'Unable to run job.';
    $response = new Response($message, 501);

    // The request we're working on.
    $request = $app['request'];
    // Error out if the sender is trying to POST a job with an ID.
    if ($request->get('id', FALSE)) {
      return new Response('Bad request: Cannot POST job start with an ID.', 400);
    }
    try {
      $job = Job::createFromRequest($request);
    }
    // If the request is insufficient, createFromRequest() will throw an exception.
    // @todo Make a better exception type.
    catch (\Exception $e) {
      return new Response($e->getMessage(), 400);
    }

    // We have to persist our Job entity in order to generate an ID. Grab the
    // entity manager service.
    $em = $app['orm.em'];
    try {
      $em->persist($job);
      $em->flush();
    }
    catch (\Exception $e) {
      // Log error, report to Results. Return error response.
      return new Response($e->getMessage(), 400);
    }

    // Start our Runner service.
    $runner = $app['runner'];
    $runner->setJob($job);
    $result = $runner->sendToJenkins($app['config']['jenkins']['token']);

    // Check the return to make sure we had a successful submission.
    if ($result === FALSE) {
      $message = 'Jenkins build was not successful.';
      $job->setResult('error');
      $job->setStatus('error');
      $response = new Response($message, 504);
    }
    else {
      $job->setStatus('building');
      $message = 'The build is in the queue at the following address: ' . $result;
      $output = [
        'message' => $message,
        'uri' => $result,
        'status' => 'building',
        'job' => json_encode($job),
      ];
      $response = new JsonResponse($output, 200);
    }
    $job->log($message);
    $em->persist($job);
    $em->flush();

    // @todo: Do something with the result of Results.
    $result = $runner->sendToResults();

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
