<?php
/**
* API to openbuildings
*/
class OB_API
{
	static protected $endpoints = array(
		'building' => 'buildings',
		'person' => 'people',
		'user' => 'users',
		'company' => 'companies',
		'comment' => 'comments',
    'media' => 'media',
    'town' => 'towns',
    'country' => 'countries',
    'data_field' => 'data_feilds',
	);

  const SERVER = "http://openbuildings.com/api-2/";

	protected $session = null;

	static public function factory($server = null)
	{
		return new OB_API($server);
	}

	public function __construct($server = null)
	{
		$this->server = $server ? $server : Kohana::$config->load('openbuildings-api.server');
	}

	public function __destruct()
	{
		if($this->session AND is_file($this->session))
		{
			unlink($this->session);
		}
	}

	public function login($user, $password)
	{
		$this->session = tempnam(sys_get_temp_dir(), "api_session");

		$response = $this->post('auth', array('auth_user' => $user, 'auth_password' => $password));	

		return $this;
	}

  public function get($url, $options = null)
  {
    $handler = $this->curl_handler($url, $options);

    $response = curl_exec($handler);
    $code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
    
    curl_close($handler);
    
    return new OB_API_Response($response, $code);
  }

  public function post($url, $options, $data)
  {
    $handler = $this->curl_handler($url, $options);
		curl_setopt($handler, CURLOPT_POSTFIELDS, $data);
    
    $response = curl_exec($handler);
    $code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
    
    curl_close($handler);
    
    return new OB_API_Response($response, $code);
  }

  protected function curl_handler($url, $options)
  {
  	$handler = curl_init();
    curl_setopt($handler, CURLOPT_URL, self::url($url, $options));
    curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
    

    if($this->session)
    {
	    curl_setopt($handler, CURLOPT_COOKIEJAR, $this->session);
	    curl_setopt($handler, CURLOPT_COOKIEFILE, $this->session);
    }

    return $handler;
  }

  static public function url($url, $options)
  {
  	$options = is_array($options) ? http_build_query($options) : $options;

  	return self::SERVER . $url.'.json?'.$options;
  }

  /**
   * HELPER METHODS
   * ======================================================
   */

  public function item($type, $id, $options = null)
  {
  	if( ! isset(self::$endpoints[$type]))
  		throw new Kohana_Exception("Invalid type :type must be one of :types", array(":type" => $type, ":types" => join(', ', array_keys(self::$endpoints))));

  	if( ! is_numeric($id))
  		throw new Kohana_Exception("Id must be numeric for :type", array(":type" => $type));

  	$response = $this->get(self::$endpoints[$type].'/'.$id, $options);

  	return OB_API_Item::factory($this, $type, $response->result);
  }

  public function listing($type, $options = null)
  {
  	if( array_search($type, self::$endpoints) === FALSE)
  		throw new Kohana_Exception("Invalid type :type must be one of :types", array(":type" => $type, ":types" => join(', ', array_values(self::$endpoints))));

  	$response = $this->get($type, $options);

  	return new OB_API_Collection($this, array_search($type, self::$endpoints), $response->result, $response->total);
  }

}