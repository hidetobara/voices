<?php
require_once( SMARTY_DIR . 'Smarty.class.php' );
require_once( 'Log.php' );


class WebException extends Exception
{
	public $location;
	public $array;
	public $module;
	function __construct( $message, $module, $array=null )	
	{
		parent::__construct( $message );
		$this->location = $this->getFile() . "#" . $this->getLine();
		$this->module = $module;
		$this->array = $array;
	}
}

class BaseWeb
{
	protected $assigned;
	protected $module;
	protected $template;
	

	function __construct( $options=null )
	{
		$this->assigned = array();
		
		$this->assignHash( array(
			'HOME_URL' => HOME_URL,
			'ENV_TYPE' => ENV_TYPE,
			) );
			
		$this->module = 'web/';
		$this->template = 'error.tpl';
	}
	
	function setModule( $module ){		$this->module = $module;		}
	function setTemplate( $tpl ){		$this->template = $tpl;		}
	
	function assign( $key, $item ){		$this->assigned[ $key ] = $item;		}
	function assignHash( $hash ){		foreach( $hash as $key => $item ) $this->assigned[ $key ] = $item;		}
	
	function run()
	{
		try
		{
			$this->initialize();
			$this->handle();
			$this->display();
			$this->finalize();
		}
		catch(WebException $ex)
		{
			$path = LOG_DIR . 'web/' . $ex->module . data('Ymd') . '.log';
			Log::singleton('file', $path, 'ERR', array('mode'=>0777))
				->log( $ex->getMessage() . " @" . $ex->location . " :" . var_export($ex->array,true) );
			$this->assign( 'message', $ex->getMessage() );
		}
	}
	
	protected function initialize()
	{
	}
	
	protected function handle()
	{
	}
	
	protected function display()
	{
		$smarty = new Smarty();
		
		$smarty->template_dir = SMARTY_WORK_DIR . 'templates/';
		$smarty->compile_dir  = SMARTY_WORK_DIR . 'templates_c/';
		$smarty->cache_dir    = SMARTY_WORK_DIR . 'cache/';
		
		$smarty->assign( $this->assigned );
		
		$path = SMARTY_TEMPLATE_DIR . $this->module . $this->template;
		if( file_exists($path) ) $smarty->display( $path );
	}
	
	protected function finalize()
	{
	}
}

?>