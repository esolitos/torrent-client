<?php

namespace Drupal\transmission_client;

use Transmission\Model\Torrent as TransmissionTorrent;

/**
 * Interface TransmissionRpcClientInterface.
 *
 * @package Drupal\transmission_client
 */
interface TransmissionRpcClientInterface {

  /**
   * Test a connection to a Transmission-Daemon returning the version of the
   * connection was successful.
   *
   * @param string $host IP/Hostname of the server
   * @param integer $port Port of the server
   * @param string $path Path to the RPC listener
   * @param string|null $auth_user Optional user if required
   * @param string|null $auth_pass Optional password if required
   * @return string|false Returns the server version if reachable or FALSE if
   *                      the connection attempt was not successful.
   */
  public function testConnection($host, $port, $path, $auth_user = NULL, $auth_pass = NULL);

  /**
   * Loads the full list of the torrents currently handled by the daemon.
   *
   * @return array
   */
  public function listAll();

  /**
   * Loads a single Torrent from the server using the torrent hash.
   *
   * @param $torrent_hash string
   *
   * @return TransmissionTorrent|false
   */
  public function getByHash($torrent_hash);

  /**
   * Loads a single Torrent from the server using the Transmission torrent's ID.
   *
   * @param $transmission_id integer
   *
   * @return TransmissionTorrent|false
   */
  public function getById($transmission_id);


  public function addTorrent(TransmissionTorrent $torrent);

}
