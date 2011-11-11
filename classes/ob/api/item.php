<?php
/**
 * API_Response
 * @package    OpenBuildings/openbuildings-api
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class OB_API_Item
{
  protected $_data;
  protected $_endpoint;
  protected $_api;

  static public function factory($api, $type, $data)
  {
  	$class = "OB_API_Item_".ucfirst($type);
  	if(class_exists("OB_API_Item_".$type))
  	{
  		return new $class($api, $type, $data);
  	}
  	else
  	{
  		return new OB_API_Item($api, $type, $data);
  	}
  }

  public function __construct($api, $type, $data = null) 
  {
  	$this->_type = $type;
  	$this->_data = (array) $data;
  	$this->_api = $api;
  }

  public function __get($name)
  {
  	return isset($this->_data[$name]) ? $this->_data[$name] : $name ;
  }

  public function __set($name, $value)
  {
  	$this->_data[$name] = $value;
  }

  public function load($id)
  {
  	$response = $this->_api->get(OB_API::$endpoints[$this->_type] . '/' . $id);
  	$this->_data = $response->result;
  	$this->loaded = true;
  	return $this;
  }

  public function set($data)
  {
  	$this->_data = $data;
  }

  public function id()
  {
  	return isset($this->_data['id']) ? $this->_data['id'] : null;
  }

  public function loaded()
  {
  	return $this->loaded;
  }  

  public function save()
  {
  	$response = $this->api->post(OB_API::$endpoints[$this->_type].($this->loaded() ? '/' . $this->id() : ''), null, $this->_data);
  	$this->_data = $response->result;
  	$this->loaded = true;

  	return $this;
  }

}