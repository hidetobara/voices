<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "program/ProgramHandler.php" );


class RankingPage extends BaseWeb
{
	const DEFAULT_NAME = 'RecentRanking';
	protected $program;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'ranking';
		$this->template = 'ranking.tpl';
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();
		
		$name = $_REQUEST['program'] ? $_REQUEST['program'] : self::DEFAULT_NAME;
		$memory = array( 'program'=>$name );
		$this->program = ProgramHandler::handleMemory( $this->userid, $memory );
		
		$this->assign( 'program', $name );
	}
	
	function handle()
	{
		if( $this->program )
		{
			$infos = $this->program->getInfos();
			$this->assign( 'media_array', $infos );
		}
	}
}
$page = new RankingPage();
$page->run();