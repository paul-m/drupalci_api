<?php

namespace API;

use Silex\Exception as Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\Request;

/**
 * Class Jenkins
 * A generic build trigger class for Jenkins remote API calls.
 */
class Jenkins {

  /**
   * @var string
   */
  protected $protocol = 'http';

  /**
   * @var string
   */
  protected $host = '';

  /**
   * @var string
   */
  protected $port = '80';

  /**
   * @var string
   */
  protected $token = '';

  /**
   * @var string
   */
  protected $build = '';

  /**
   * @var array
   */
  protected $query = array();

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client = false;

  /**
   *
   * @param \GuzzleHttp\Client $guzzle_client
   */
  public function __construct($guzzle_client) {
    $this->setClient($guzzle_client);
  }

  /**
   * Helper function to build the URL of the Jenkins host.
   */
  protected function buildUrl() {
    $protocol = $this->getProtocol();
    $host = $this->getHost();
    $port = $this->getPort();
    $build = $this->getBuild();
    return $protocol . '://' . $host . ':' . $port . '/job/' . $build . '/buildWithParameters';
  }

  /**
   * Helper function to build the request.
   */
  public function sendRequest() {
    // Ensure we have a host.
    $host = $this->getHost();
    if (!$host) {
      return 'Please provide a Jenkins host.';
    }

    // Ensure we have a build.
    $build = $this->getBuild();
    if (!$build) {
      return 'Please provide a Jenkins build.';
    }

    // Ensure we have a port.
    $port = $this->getPort();
    if (!$port) {
      return 'Please provide a Jenkins port.';
    }

    // Add the Token to the query if it is set.
    $token = $this->getToken();
    $query = $this->getQuery();
    if ($token) {
      $query['token'] = $token;
    }

    // Post the request to Jenkins.
    $url = $this->buildUrl();
    $client = $this->getClient();
    $response = $client->get($url, [
      // @todo, Once we get signed certificates we should remove.
      'verify' => false,
      'query' => $this->getQuery(),
    ]);

    return $response;
  }

  /**
   * Send the data to the remote Jenkins host.
   */
  public function send() {
    $response = $this->sendRequest();

    // We get the location of the build in the queue so we can track it.
    // First we make sure it is in the right format.
    $url = $this->buildUrl();
    $location = $response->getHeader('Location');
    if (strpos($location, $url)) {
      return false;
    }

    return $location;
  }

  /**
   * @return string
   */
  public function getBuild() {
    return $this->build;
  }

  /**
   * @param string $build
   */
  public function setBuild($build) {
    $this->build = $build;
  }

  /**
   * @return \GuzzleHttp\Client
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * @param \GuzzleHttp\Client $client
   */
  public function setClient($client) {
    $this->client = $client;
  }

  /**
   * @return string
   */
  public function getHost() {
    return $this->host;
  }

  /**
   * @param string $host
   */
  public function setHost($host) {
    $this->host = $host;
  }

  /**
   * @return string
   */
  public function getPort() {
    return $this->port;
  }

  /**
   * @param string $port
   */
  public function setPort($port) {
    $this->port = $port;
  }

  /**
   * @return string
   */
  public function getProtocol() {
    return $this->protocol;
  }

  /**
   * @param string $protocol
   */
  public function setProtocol($protocol) {
    $this->protocol = $protocol;
  }

  /**
   * @return array
   */
  public function getQuery() {
    return $this->query;
  }

  /**
   * @param array $query
   */
  public function setQuery($query) {
    $token = $this->getToken();
    if ($token) {
      $query['token'] = $token;
    }
    $this->query = $query;
  }

  /**
   * @return string
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * @param string $token
   */
  public function setToken($token) {
    $this->token = $token;
  }

}
