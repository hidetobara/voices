<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );


class PlaylistWeb extends BaseWeb
{	
	protected $mode;
	protected $step;
	
	protected $info;
	protected $db;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'playlist';
		$this->template = 'playlist.tpl';
		
		$this->db = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
	}
	
	function initialize()
	{
		$this->checkSession();

		$this->info = new PlaylistInfo( $_REQUEST );
		$this->info->userid = $this->userid;
		$this->mode = 'all';
	}
	
	function handle()
	{
		switch( $_REQUEST['command'] )
		{
			case 'edit':
			case 'update':	
			case 'delete':
				$this->handleEdit( $_REQUEST['command'] );
				break;
			
			case 'new':
				$this->info = $this->db->newInfo( $this->info );

			case 'all':
			default:
				$array = $this->db->getUserInfos( $this->userid );
				$this->assign( "playlist_array", $array );

				break;
		}
		$this->assign( "mode", $this->mode );
		$this->assign( "step", $this->step );
		$this->assign( "playlist_info", $this->info );		
	}

	function handleEdit( $command )
	{
		if( !$this->info->playlistid ) return;

		$this->mode = 'edit';

		if( $command == "update" )
		{
			$this->db->updateInfo( $this->info );
			$this->assign( "message", "update !" );
			$this->step = 'updated';
		}
		else if( $command == "delete" )
		{
			$this->db->deleteInfo( $this->info );
			$this->assign( "message", "delete !" );
			$this->step = 'deleted';
		}
		else
		{
			$this->info = $this->db->getInfo( $this->info->playlistid );
		}
	}
}
$web = new PlaylistWeb();
$web->run();
?>