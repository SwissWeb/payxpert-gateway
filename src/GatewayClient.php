<?php

namespace PayXpert\Gateway;

/**
 * Client for the PayXpert Payment Gateway
 *
 * PHP dependencies:
 * PHP >= 5.2.0
 * PHP CURL library
 * PHP JSON extension
 *
 * @version 0207-1 2016-12-13
 * @copyright Payxpert 2012-2016
 * @package GatewayClient
 *
 */

/**
 * Client class for the PayXpert Gateway
 *
 * This class allows a client to manage several transactions with the PayXpert
 * Gateway.
 */
class GatewayClient {
  private $url;
  private $originatorID;
  private $password;
  private $proxy;
  private $extraCurlOptions = array();

  /**
   * Create a new Gateway with optional global user and password
   *
   * @param string $url;
   * @param string|null $originatorID
   * @param string|null $password
   */
  public function __construct($url, $originatorID = null, $password = null) {
    $this->url = $url;
    $this->originatorID = $originatorID;
    $this->password = $password;
  }

  /**
   * Sets the proxy information
   *
   * Optional
   *
   * @param string|null $host
   * @param string|null $port
   * @param string|null $username
   * @param string|null $password
   */
  public function setProxy($host = null, $port = null, $username = null, $password = null) {
    $this->proxy = new \stdClass();
    $this->proxy->host = $host;
    $this->proxy->port = $port;
    $this->proxy->username = $username;
    $this->proxy->password = $password;
  }

  /**
   * Add extra curl options (internal use only)
   */
  public function setExtraCurlOption($name, $value) {
    $this->extraCurlOptions[$name] = $value;
  }

  /**
   * Creates a new request
   *
   * @param string $type
   * @param string|null $originatorID
   *          must be set if wasn't set in constructor
   * @param string|null $password
   *          must be set if wasn't set in constructor
   */
  public function newTransaction($type, $originatorID = null, $password = null) {
    $originatorID = isset($originatorID) ? $originatorID : $this->originatorID;
    $password = isset($password) ? $password : $this->password;

    return new GatewayTransaction($type, $originatorID, $password, $this->proxy, $this->url, $this->extraCurlOptions);
  }
}
