<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "File/ImageFile.php" );
require_once( INCLUDE_DIR . "web/ShortSession.php" );


class PlaylistWeb extends BaseWeb
{	
	protected $mode;
	protected $step;
	
	protected $info;
	protected $db;

	protected $imageFile;
	protected $imageParam;
	protected $imageDb;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'playlist';
		$this->template = 'playlist.tpl';
		
		$this->db = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
		
		$this->imageFile = $opt['ImageFile'] ? $opt['ImageFile'] : new ImageFile();
		$this->imageDb = $opt['ImageInfoDB'] ? $opt['ImageInfoDB'] : new ImageInfoDB();
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();

		$this->mode = 'all';		
	}
	
	function handle()
	{
		switch( $_REQUEST['command'] )
		{
			case 'edit':
			case 'update':	
			case 'delete':
				ShortSession::get()->check();
				$this->handleEdit( $_REQUEST['command'] );
				break;
			
			case 'new':
				ShortSession::get()->check();
				$this->handleNew();
				$this->assignPlaylistArray();
				break;

			case 'all':
			default:
				ShortSession::get()->updateCookie();
				$this->assignPlaylistArray();
				break;
		}
		$this->assign( "mode", $this->mode );
		$this->assign( "step", $this->step );
		$this->assign( "playlist_info", $this->info );		
	}

	protected function assignPlaylistArray()
	{
		$array = $this->db->getUserInfos( $this->userid );
		$this->assign( "playlist_array", $array );
	}
	
	function handleNew()
	{
		$this->info = new PlaylistInfo( $_REQUEST );
		$this->info->userid = $this->userid;
		$this->info = $this->db->newInfo( $this->info );		
	}
	
	function handleEdit( $command )
	{
		$this->mode = 'edit';
		$pid = $_REQUEST['playlist_id'];
		
		if( $command == "update" )
		{
			$this->info = $this->db->getInfo( $pid );
			$infoNew = new PlaylistInfo( $_REQUEST );
			
			$this->info->title = $infoNew->title;	/////copy title

			$imageFile = $_FILES['image_file'];
			if( $imageFile['size'] > 0 )
			{
				$imageInfo = $this->imageFile->save( $this->userid, $imageFile );
				$this->info->imageid = $imageInfo->imageid;
			}
			
			$this->db->updateInfo( $this->info );
			$this->step = 'updated';
		}
		else if( $command == "delete" )
		{
			$this->info = new PlaylistInfo( $_REQUEST );
			if( !$this->info->playlistid ) return;

			$this->db->deleteInfo( $this->info );
			$this->step = 'deleted';
		}
		else
		{
			$this->info = $this->db->getInfo( $pid );
		}
	}
}
$web = new PlaylistWeb();
$web->run();
?>