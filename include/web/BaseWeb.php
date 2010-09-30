<?php
require_once( SMARTY_DIR . 'Smarty.class.php' );
require_once( 'Log.php' );
require_once( INCLUDE_DIR . 'VoiceException.php' );
require_once( INCLUDE_DIR . "web/LoginSession.php" );
require_once( INCLUDE_DIR . "web/CommonMessages.php" );


class BaseWeb
{
	protected $userid;
	
	protected $assigned;
	protected $templateGroup;
	protected $name;
	protected $template;
	protected $redirectUrl;
	
	function __construct( $opt=null )
	{
		$this->assigned = array();
		
		$this->assignHash( array(
			'HOME_URL' => HOME_URL,
			'SECURE_HOME_URL' => SECURE_HOME_URL,
			'ENV_TYPE' => ENV_TYPE,
			'API_URL' => API_URL ) );
			
		$this->templateGroup = 'web';
		$this->name = 'default';
		$this->template = 'error.tpl';
	}
	
	function setGroup( $group ){		$this->templateGroup = $group;		}
	function setTemplate( $tpl ){		$this->template = $tpl;		}
	
	function assign( $key, $item ){		$this->assigned[ $key ] = $item;		}
	function assignHash( $hash )
	{
		if( !is_array($hash) || count($hash)==0 ) return;
		foreach( $hash as $key => $item ) $this->assigned[ $key ] = $item;
	}
	
	function setRedirect( $url ){		$this->redirectUrl = $url;		}
	
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
				->log( $ex->getMessage() . " @" . $ex->location . " : DUMP=" . json_encode($ex->array) );
			$this->assign( 'error', $ex->getMessage() );
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
	protected function assignSession()
	{
		$this->assignHash( LoginSession::get()->getSessionArray() );
	}
	
	protected function initialize()
	{
	}
	
	protected function handle()
	{
	}
	
	protected function display()
	{
		if( $this->redirectUrl )
		{
			header( sprintf("Location: %s", $this->redirectUrl) );
			return;
		}
		
		$smarty = new Smarty();		
		$smarty->template_dir = SMARTY_TEMPLATE_DIR;
		$smarty->compile_dir  = SMARTY_WORK_DIR . 'templates_c/';
		$smarty->cache_dir    = SMARTY_WORK_DIR . 'cache/';
		$smarty->plugins_dir[] = SMARTY_TEMPLATE_DIR . 'plugins/';
		$smarty->assign( $this->assigned );
		
		$path = SMARTY_TEMPLATE_DIR . $this->templateGroup . '/' . $this->template;
		if( file_exists($path) ) $smarty->display( $path );
	}
	
	protected function finalize()
	{
	}
}
?>