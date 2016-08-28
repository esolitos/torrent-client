<?php

namespace Drupal\transmission_client;

use Drupal\Core\Config\ConfigFactory;
use Psr\Log\LoggerInterface;
use Transmission\Client as TransmissionClient;
use Transmission\Client;
use Transmission\Model\Torrent as TransmissionTorrent;
use Transmission\Transmission;

/**
 * Class TransmissionRpcClient.
 *
 * @package Drupal\transmission_client
 */
class TransmissionRpcClient implements TransmissionRpcClientInterface {

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \Transmission\Transmission
   */
  private $rpc;

  /**
   * Constructor.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(ConfigFactory $config_factory, LoggerInterface $logger) {
    $this->configFactory = $config_factory;
    $this->logger = $logger;

    // Connect to the RPC Server
    $this->connect();
  }

  /**
   * @inheritDoc
   */
  public function testConnection($host, $port, $path, $auth_user = NULL, $auth_pass = NULL) {
    $current_rpc_client = clone $this->rpc->getClient();

    $test_client = new Client($host, $port, $path);
    if ($auth_user) {
      $test_client->authenticate($auth_user, $auth_pass);
    }

    $this->rpc->setClient($test_client);

    try {
      return $this->version();
    }
    catch (\RuntimeException $e) {
      // Do nothing, connection was unsuccessful.
    }
    finally {
      $this->rpc->setClient($current_rpc_client);
    }

    return FALSE;
  }

  /**
   * @inheritDoc
   */
  public function listAll() {
    return [];
  }

  /**
   * @inheritDoc
   */
  public function getByHash($torrent_hash) {

  }

  /**
   * @inheritDoc
   */
  public function getById($transmission_id) {

  }

  /*
   * PROTECTED METHODS
   */

  protected function getSessionInfo() {
    return $this->customCall('session-get', []);
  }

  protected function version() {
    $session = $this->getSessionInfo();

    $version = [];
    if (isset($session->version)) {
      $version[] = sprintf('Transmission Daemon: %s', $session->version);
    }
    if (isset($session->{'rpc-version'})) {
      $version[] = sprintf('RPC Version: %s', $session->{'rpc-version'});
    }

    return implode(', ', $version);
  }

  /*
   * PRIVATE METHODS
   */

  private function connect() {
    $config = $this->configFactory->get('transmission_client.config');

    if ( $config->get('rpc.ip') ) {
      $rpc_client = new TransmissionClient($config->get('rpc.ip'), $config->get('rpc.port'), $config->get('rpc.path'));

      if ($config->get('rpc.auth')) {
        $rpc_client->authenticate($config->get('rpc.auth.user'), $config->get('rpc.auth.pass'));
      }

      $this->rpc = new Transmission();
      $this->rpc->setClient($rpc_client);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Calls the RPC server with a custom function not available in Transmission.
   *
   * @param string $method The method to call.
   * @param array $args Array of arguments.
   * @return mixed The response or FALSE in case of failure.
   */
  private function customCall($method, array $args) {
    try {
      $response = $this->rpc->getClient()->call($method, []);

      if ( $response->result == 'success' ) {
        return $response->arguments;
      }
      else {
        $this->logger->notice(
          'RPC Call responded: "@message" to %method called with @args',
          [
            '@message'=>$response->result,
            '%method'=>$method,
            '@args'=>serialize($args)
          ]
        );
      }

    } catch (\RuntimeException $e) {
      $this->logger->error(
        'RPC Call %method throw exception: "@message"',
        ['%method'=>$method, '@message'=>$e->getMessage()]
      );
    }

    return NULL;
  }

}
