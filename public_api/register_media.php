<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "web/BaseApi.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "DB/MediaInfo.php" );
require_once( INCLUDE_DIR . "web/ShortSession.php" );


class RegisterMediaApi extends BaseApi
{
	protected $playlistDb;
	
	protected $mid;
	protected $lid;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->playlistDb = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->checkFormat( 'json' );
		
		$this->mid = $_REQUEST['mid'];
		$this->lid = $_REQUEST['lid'];
		if( !is_string($this->mid) || !is_numeric($this->lid) ) throw new VoiceException("Invalid parameters");
	}
	
	function handle()
	{
		$media = MediaInfo::getInfo( $this->mid );
		if( !$media || !$media->isVisible ) throw new VoiceException("No media");
		
		$playlist = $this->playlistDb->getInfo( $this->lid );
		if( !$playlist ) throw new VoiceException("No playlist");
		
		if( $playlist->userid != $this->userid ) throw new VoiceException("Mismatch userid");
		
		$playlist->addMediaId( $this->mid );
		$this->playlistDb->updateInfo( $playlist );
		
		$this->assign( 'status', 'ok' );
		$this->assign( 'title', $playlist->title );
	}
}
$api = new RegisterMediaApi();
$api->run();
?>