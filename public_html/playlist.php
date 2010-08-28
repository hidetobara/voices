<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "File/ImageFile.php" );


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
				$this->handleNew();
				$this->assignPlaylistArray();
				break;

			case 'all':
			default:
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
		$this->info = new PlaylistInfo( $_REQUEST );
		if( !$this->info->playlistid ) return;

		$this->mode = 'edit';
		if( $command == "update" )
		{
			$this->info = $this->db->getInfo( $this->info->playlistid );
			$this->title = $_REQUEST['title'];
						
			if( $_FILES['image_file'] )
			{	
				$imageInfo = $this->imageDb->newInfo( $this->userid );
				$this->imageFile->save( $_FILES['image_file'], $imageInfo );
				$this->info->imageid = $imageInfo->imageid;
			}
			
			$this->db->updateInfo( $this->info );
			$this->step = 'updated';
		}
		else if( $command == "delete" )
		{
			$this->db->deleteInfo( $this->info );
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