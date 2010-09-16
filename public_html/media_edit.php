<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class VoiceEditWeb extends BaseWeb
{
	protected $playlistDb;
	protected $voiceDb;
	protected $playlistArray;
	protected $info;
	
	protected $mode;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );

		$this->playlistDb = new PlaylistInfoDB();
		
		$this->name = 'media_edit';
		$this->template = 'media_edit.tpl';		
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();

		$this->info = MediaInfo::getInfo( $_REQUEST['mid'] );
		if( !$this->info ) throw new VoiceException(CommonMessages::get()->msg('INVALID_PARAMETER'));	
		
		if( is_a($this->info,"VoiceInfo") )
		{
			$voiceDb = new VoiceInfoDB();
			$voiceDb->getDetail( $this->info );
		
			$this->assign( "target_voice", $this->info );
		}
		
		$this->playlistArray = $this->playlistDb->getUserInfos( $this->userid );
		$this->assign( "playlist_array", $this->playlistArray );	
	}
	
	function handle()
	{
		$command = $_REQUEST['command'];
		$pid = $_REQUEST['playlist_id'];
		if( $command == "register_playlist" && $pid )
		{
			$playlistInfo = $this->playlistDb->getInfo( $pid );
			$playlistInfo->addMediaId( $this->info->mediaid );
			$this->playlistDb->updateInfo( $playlistInfo );
			$this->assign( "target_playlist", $playlistInfo );
			$this->mode = "registered_playlist";
		}
		
		$this->assign( "mode", $this->mode );
	}
}
$web = new VoiceEditWeb();
$web->run();