<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "web/SessionInfo.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );

class PlaylistWeb extends BaseWeb
{
	protected $mode;
	const MODE_NEW_PLAYLIST = 1;
	
	protected $info;
	protected $db;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->module = 'web';
		$this->name = 'playlist';
		$this->template = 'playlist.tpl';
		
		$this->db = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
	}
}
$web = new PlaylistWeb();
$web->run();
?>