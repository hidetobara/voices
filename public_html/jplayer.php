<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );


class PlayerWeb extends BaseWeb
{
	public $mode;
	public $playlistDb;
	
	public $memory;
	public $mid;
	public $urlNext;
	
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
			if( $this->memory['mode'] == 'Playlist' ) $this->mode = 'Playlist';
			return;
		}
		if( $_REQUEST['playlist_id'] )
		{
			$pid = $_REQUEST['playlist_id'];
			$this->memory = array('mode'=>'Playlist','pid'=>$pid);
			$this->mode = 'Playlist';
			return;
		}
		if( $_REQUEST['mid'] )
		{
			$this->mid = $_REQUEST['mid'];
			return;
		}
	}
	
	function handle()
	{		
		if($this->mode == 'Playlist')
		{
			$playlistInfo = $this->playlistDb->getInfo( $this->memory['pid'] );
			if( !$playlistInfo ) throw new VoiceException(CommonMessages::get()->msg('NO_PLAYLIST_INFO'));

			$index = $this->memory['index'] ? $this->memory['index'] : 0;
			$this->mid = $playlistInfo->getMediaId( $index );

			$nextMid = $playlistInfo->getMediaId( $index + 1 );
			if( $nextMid )
			{
				$this->memory['index'] = $index + 1;
				$string = urlencode(json_encode($this->memory));
				$this->assign( 'url_next', sprintf("%sjplayer.php?memory=%s",HOME_URL,$string) );
			}
		}
		
		$info = MediaInfo::getInfo( $this->mid, array('detail'=>true) );

		if( !$info->imageid && $playlistInfo->imageid ) $info->imageid = $playlistInfo->imageid;	///// modify imageid
		
		if( is_a($info,"VoiceInfo") )
		{
			if( !$info ) throw new VoiceException(CommonMessages::get()->msg('NO_VOICE_INFO'));
			
			$apiUrl = sprintf("%smedia.php?id=%s",API_URL,$info->mediaid);
			$this->assign( 'media_info', $info );
			$this->assign( 'api_now', $apiUrl );
		}
	}
}
$web = new PlayerWeb();
$web->run();
