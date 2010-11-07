<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );
require_once( INCLUDE_DIR . "web/ShortSession.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "program/ProgramHandler.php" );


class PlayerWeb extends BaseWeb
{
	public $memory;
	public $media;
	
	public $playlistDb;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'player';
		$this->template = 'jplayer.tpl';	

		$this->playlistDb = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();

		if( $_REQUEST['memory'] )
		{
			$string = $_REQUEST['memory'];
			$string = str_replace('\\"', '"', $string);	/////bug of browzer ?
			$this->memory = json_decode($string, true);
		}
		if( $_REQUEST['program'] )
		{
			$this->memory = array('program'=>$_REQUEST['program']);
		}
		if( $_REQUEST['playlist_id'] )
		{
			$this->memory = array('program'=>PlaylistProgram::NAME,'pid'=>$_REQUEST['playlist_id']);
			return;
		}
		if( $_REQUEST['mid'] )
		{
			$mid = $_REQUEST['mid'];
			$this->media = MediaInfo::getInfo( $mid, array('detail'=>true) );
			return;
		}
	}
	
	function handle()
	{
		$this->assign( 'api_url', API_URL );
		
		if( $this->userid )
		{
			$playlistArray = $this->playlistDb->getUserInfos( $this->userid );
			$this->assign( "playlist_array", $playlistArray );
		}
		
		if( $this->memory ) $this->handleProgram();

		if( is_a($this->media,"VoiceInfo") )
		{
			$key = LoginSession::get()->getTempKey();
			$apiMedia = sprintf("%smedia/%d/%s/%s.mp3",
				API_URL,
				$key->userid,
				$key->tempKey,
				$this->media->mediaid );
			$this->assign( 'media_info', $this->media );
			$this->assign( 'api_media', $apiMedia );
		}
	}
	
	private function handleProgram()
	{
		$program = ProgramHandler::handleMemory( $this->userid, $this->memory );
		if( !$program ) return;
		
		$this->media = $program->currentInfo();

		if( $program->previousInfo() )
		{
			$memory = $this->memory;
			$memory['index'] = $program->index - 1;
			$string = urlencode(json_encode($memory));
			$this->assign( 'url_previous', sprintf("%sjplayer.php?memory=%s",HOME_URL,$string) );
		}
		if( $program->nextInfo() )
		{
			$memory = $this->memory;
			$memory['index'] = $program->index + 1;
			$string = urlencode(json_encode($memory));
			$this->assign( 'url_next', sprintf("%sjplayer.php?memory=%s",HOME_URL,$string) );
		}
	}
}
$web = new PlayerWeb();
$web->run();
