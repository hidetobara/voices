<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );

class MyTop extends BaseWeb
{
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'top';
		$this->template = 'top.tpl';
	}
	
	function initialize()
	{
		$this->assignSession();
	}
}
$page = new MyTop();
$page->run();
?>