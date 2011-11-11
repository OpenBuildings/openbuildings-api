<?php
/**
 * Open Buildings Exception
 * @package    OpenBuildings/openbuildings-api
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class OB_API_Exception extends Kohana_Exception
{
  public $code;
  public $response;

  public function __construct($response, $code) 
  {
    $this->code = $code;
    $this->response = $response;
    $this->message = "API Exception $code";
  }

}