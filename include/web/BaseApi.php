<?php
require_once( 'Log.php' );
require_once( INCLUDE_DIR . 'VoiceException.php' );
require_once( INCLUDE_DIR . "web/LoginSession.php" );
require_once( INCLUDE_DIR . "web/CommonMessages.php" );

class BaseApi
{
	protected $userid;
	protected $array;
	protected $format;
	
	function __construct( $opt=null )
	{
		$this->array = array( 'status' => 'undefined' );
		$this->format = "xml";
	}
	
	function assign( $name, $value )
	{
		$this->array[ $name ] = $value;
	}
	
	function run()
	{
		try
		{
			$this->initialize();
			$this->handle();
		}
		catch(VoiceException $ex)
		{
			$path = LOG_DIR . 'web/' . $this->name . date('Ymd') . '.log';
			Log::singleton('file', $path, 'ERR', array('mode'=>0777))
				->log( $ex->getMessage() . " @" . $ex->location . " :" . var_export($ex->array,true) );
			$this->array = array(
				'status' => 'fail',
				'error' => $ex->getMessage() );
		}
		$this->display();
		$this->finalize();
	}
	
	protected function checkSession()
	{
		$userid = (int)LoginSession::get()->check();
		if( !$userid ) throw new VoiceException(CommonMessages::get()->msg('NO_SESSION'));

		$this->userid = $userid;
	}
	protected function checkFormat( $default=null )
	{
		$this->format = $_REQUEST['format'];
		$validFormats = array( 'xml', 'json' );
		if( !in_array($this->format,$validFormats) ) $this->format = $default;
	}
	
	protected function initialize()
	{
	}
	
	protected function handle()
	{
	}
	
	protected function display()
	{
		switch($this->format)
		{
			case 'xml':
				$context = $this->toXml( $this->array, 'voice' );				
				header( "Content-type: text/xml" );
				header( "Content-Length: " . count($context) );
				print $context;
				break;
				
			case 'json':
				print json_encode( $this->array );
		}
	}
	
	protected function finalize()
	{
	}
	
	/**
	 * The main function for converting to an XML document.
	 * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
	 * @param array $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @param SimpleXMLElement $xml - should only be used recursively
	 * @return string XML
	 */
	protected function toXml( $data, $rootNodeName = 'data', $xml=null )
	{
		if ($xml == null) $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
 
		// loop through the data passed in.
		foreach($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = "unknownNode_". (string) $key;
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_]/i', '', $key);
			 
			// if there is another array found recrusively call this function
			if (is_array($value))
			{
				$node = $xml->addChild($key);
				// recrusive call.
				$this->toXml($value, $rootNodeName, $node);
			}
			else 
			{
				// add single node.
				$value = htmlspecialchars($value);
				$xml->addChild($key,$value);
			} 
		}
		// pass back as string. or simple xml object if you want!
		return $xml->asXML();
	}
}