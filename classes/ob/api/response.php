<?php
/**
 * Open Buildings API Response
 * @package    OpenBuildings/openbuildings-api
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class OB_API_Response
{
  private $container = array();
  public $result;
  public $status;
  public $total;

  public function __construct($response, $code) 
  {
    $response = json_decode($response);

    if($code == 200 AND $response->status == 'OK' AND isset($response->result))
    {
      $this->status = $response->status;
      $this->result = $response->result;


      if(is_array($response->result))
      {
        $this->total = $response->count;
      }
    }
    else
    {
      throw new OB_API_Exception($response, $code);
    }
  }
}