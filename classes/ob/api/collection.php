<?php
/**
 * Open Buildings API Collection
 * @package    OpenBuildings/openbuildings-api
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class OB_API_Collection implements ArrayAccess, Iterator, Countable 
{
	private $container = array();
	private $_total = null;
	private $_api;
	private $_type;

	public function __construct($api, $type, $container, $total) 
	{
		foreach ($container as $i => $item) 
		{
			$this->container[$i] = OB_API_Item::factory($api, $type, $item);
		}

		$this->_api = $api;
		$this->_type = $type;
		$this->_total = $total;
	}

	public function total()
	{
		return $this->_total;
	}

	public function type()
	{
		return $this->_type;
	}	

	/**
	 * Return the raw attributes array
	 * @return array
	 */
	public function as_array()
	{
		return $this->container;
	}

	public function offsetSet($offset, $value) 
	{
		if ($offset == "") 
		{
			$this->container[] = $value;
		}
		else 
		{
			$this->container[$offset] = $value;
		}
	}

	public function offsetExists($offset) 
	{
	 return isset($this->container[$offset]);
	}

	public function offsetUnset($offset) 
	{
		unset($this->container[$offset]);
	}

	public function offsetGet($offset) 
	{
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}

	public function rewind() 
	{
		reset($this->container);
	}

	public function current() 
	{
		return current($this->container);
	}

	public function key() 
	{
		return key($this->container);
	}

	public function next() 
	{
		return next($this->container);
	}

	public function valid() 
	{
		return $this->current() !== false;
	}    

	public function count() 
	{
	 return count($this->container);
	}

}