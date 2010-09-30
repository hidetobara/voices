<?php
require_once( "../../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );


class ManageTopWeb extends BaseWeb
{
	private $userDb;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'manage';
		$this->templateGroup = 'manage';
		$this->template = 'top.tpl';
	}
	
	function initialize()
	{
		$userid = (int)LoginSession::get()->check();
		if( !$userid || $userid >= MANAGER_ID_MAX )
		{
			$this->setRedirect( HOME_URL );
			return;
		}		
	}
}

$web = new ManageTopWeb();
$web->run();

?>