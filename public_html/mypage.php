<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );


class MyPage extends BaseWeb
{
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'mypage';
		$this->template = 'mypage.tpl';
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();
	}
}
$page = new MyPage();
$page->run();