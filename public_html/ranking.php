<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "web/RankingGenerator.php" );


class RankingPage extends BaseWeb
{
	protected $mode;
	const MODE_RECENT = 'recent';
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'ranking';
		$this->template = 'ranking.tpl';
	}
	
	function initialize()
	{
		$this->assignSession();
		
		$this->mode = self::MODE_RECENT;
	}
	
	function handle()
	{
		$generator = RankingGenerator::factory( $this->mode );
		
		if( $generator )
		{
			$infos = $generator->get();
			$this->assign( 'media_array', $infos );
		}
		
		$this->assign( 'mode', $this->mode );
	}
}
$page = new RankingPage();
$page->run();