<?php
/**
 * API_Response
 * @package    OpenBuildings/openbuildings-api
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class OB_API_Item_Building extends OB_API_Item
{

  public function involved_people()
  {
    $response = $this->_api->get("buildings/".$this->id()."/involved_people");

    return new OB_API_Collection($this, 'person', $response->result, $response->total);
  }

  public function involved_companies()
  {
    $response = $this->_api->get("buildings/".$this->id()."/involved_companies");

    return new OB_API_Collection($this, 'company', $response->result, $response->total);
  }

  public function comments()
  {
    $response = $this->_api->get("buildings/".$this->id()."/comments");

    return new OB_API_Collection($this, 'comment', $response->result, $response->total);
  }

  public function slug()
  {
    return $this->custom_url.'-profile-'.$this->id;
  }
}