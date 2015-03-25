<?php

namespace API\Services;

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
  protected $client = NULL;

  /**
   *
   * @param string $host
   * @param string $port
   * @param string $protocol
   */
  public function __construct($host = 'localhost', $port = '80', $protocol = 'http') {
    $this->host = $host;
    $this->port = $port;
    $this->protocol = $protocol;
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
   * Send the data to the remote Jenkins host.
   */
  public function send() {
    if (!$this->getBuild() || !$this->getToken()) {
      // @todo: Create a better exception class here.
      throw new \Exception('This Jenkins job needs a build and a token.');
    }
    $client = $this->getClient();
    $url = $this->buildUrl();
    // Send the request to Jenkins.
    $response = $client->get(
      $url,
      [
        // @todo, Once we get signed certificates we should remove.
        'verify' => false,
        'query' => $this->getQuery(),
      ]
    );
    // @todo: figure out logging error messages from guzzle.
    // We get the location of the build in the queue so we can track it.
    // First we make sure it is in the right format.
    $location = $response->getHeader('Location');
    if (strpos($location, $url)) {
      return FALSE;
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
    // Create a client if there isn't one already.
    if (empty($this->client)) {
      $this->client = new GuzzleClient();
    }
    return $this->client;
  }

  /**
   * @param \GuzzleHttp\Client $client
   */
  public function setClient(GuzzleClient $client) {
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
    // Make sure the token is included in the query.
    $token = $this->getToken();
    if ($token) {
      $this->query['token'] = $token;
    }
    return $this->query;
  }

  /**
   * @param array $query
   */
  public function setQuery($query) {
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
