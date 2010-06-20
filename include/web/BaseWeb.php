<?php
require_once( SMARTY_DIR . 'Smarty.class.php' );
require_once( 'Log.php' );
require_once( INCLUDE_DIR . 'VoiceException.php' );


class BaseWeb
{
	protected $userid = 1;	
	
	protected $assigned;
	protected $module;
	protected $name;
	protected $template;

	function __construct( $opt=null )
	{
		$this->assigned = array();
		
		$this->assignHash( array(
			'HOME_URL' => HOME_URL,
			'ENV_TYPE' => ENV_TYPE,
			'API_URL' => API_URL,
			) );
			
		$this->module = 'web';
		$this->name = 'default';
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
		}
		catch(VoiceException $ex)
		{
			$path = LOG_DIR . 'web/' . $this->name . date('Ymd') . '.log';
			Log::singleton('file', $path, 'ERR', array('mode'=>0777))
				->log( $ex->getMessage() . " @" . $ex->location . " :" . var_export($ex->array,true) );
			$this->assign( 'error', $ex->getMessage() );
		}
		$this->display();
		$this->finalize();
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
		
		$path = SMARTY_TEMPLATE_DIR . $this->module . '/' . $this->template;
		if( file_exists($path) ) $smarty->display( $path );
	}
	
	protected function finalize()
	{
	}
}

?>