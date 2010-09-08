<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );

class RankingPage extends BaseWeb
{
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'ranking';
		$this->template = 'ranking.tpl';
	}
	
	function initialize()
	{
		$this->assignSession();
	}
}
$page = new RankingPage();
$page->run();