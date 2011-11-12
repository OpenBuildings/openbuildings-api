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
	private $_item_type;


	/**
	 * Create a collection of OB API Items, iterate only on one item so that takes less memory
	 * 
	 * @param OB_API $api a referance to the api
	 * @param string $type the type of the item
	 * @param array $container results returned
	 * @param int $total total results of the query (without pagination)
	 * @return OB_API_Collection
	 */
	public function __construct(OB_API $api, $type, array $container, $total) 
	{
		if( ! is_array($container))
			throw new Kohana_Exception("API Collection must be set with an array result");

		$this->container = $container;
		$this->_item_type = OB_API_Item::factory($api, $type);

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
	 * Return all of the rows in the result as an array.
	 *
	 *     // Indexed array of all rows
	 *     $rows = $result->as_array();
	 *
	 *     // Associative array of rows by "id"
	 *     $rows = $result->as_array('id');
	 *
	 *     // Associative array of rows, "id" => "name"
	 *     $rows = $result->as_array('id', 'name');
	 *
	 * @param   string  column for associative keys
	 * @param   string  column for values
	 * @return  array
	 */
	public function as_array($key = NULL, $value = NULL)
	{
		$results = array();

		if ($key === NULL AND $value === NULL)
		{
			// Indexed rows
			foreach ($this as $row)
			{
				$results[] = $row;
			}
		}
		elseif ($key === NULL)
		{
			// Indexed columns

			foreach ($this->container as $row)
			{
				$results[] = $row->$value;
			}
		}
		elseif ($value === NULL)
		{
			// Associative rows

			foreach ($this as $row)
			{
				$results[$row->$key] = $row;
			}
		}
		else
		{
			// Associative columns
			foreach ($this->container as $row)
			{
				$results[$row->$key] = $row->$value;
			}
		}

		$this->rewind();

		return $results;
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
		return isset($this->container[$offset]) ? $this->_load($this->container[$offset]) : null;
	}

	public function rewind() 
	{
		reset($this->container);
	}

	public function current() 
	{
		return $this->_load(current($this->container));
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
		return current($this->container) !== false;
	}    

	public function count() 
	{
	 return count($this->container);
	}

	protected function _load($values)
	{
		if ($this->_item_type)
		{
			$item = clone $this->_item_type;
			// Don't return items when we don't have one
			return $values
			        ? $item->set($values)->loaded(TRUE)
			        : $item->clear();
		}

		return $values;
	}	

}