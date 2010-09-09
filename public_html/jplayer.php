<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "web/RankingGenerator.php" );


class PlayerWeb extends BaseWeb
{
	public $mode;
	const MODE_PLAYLIST = 'Playlist';
	const MODE_RANKING = 'Ranking';
	
	public $playlistDb;
	
	public $memory;
	public $media;
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
			if( $this->memory['mode'] == self::MODE_PLAYLIST ) $this->mode = self::MODE_PLAYLIST;
			if( $this->memory['mode'] == self::MODE_RANKING ) $this->mode = self::MODE_RANKING;
			return;
		}
		if( $_REQUEST['playlist_id'] )
		{
			$this->memory = array('mode'=>self::MODE_PLAYLIST,'pid'=>$_REQUEST['playlist_id']);
			$this->mode = self::MODE_PLAYLIST;
			return;
		}
		if( $_REQUEST['ranking'] )
		{
			$this->memory = array('mode'=>self::MODE_RANKING, 'type'=>$_REQUEST['ranking']);
			$this->mode = self::MODE_RANKING;
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
		if($this->mode == self::MODE_PLAYLIST) $this->handlePlaylist();
		if($this->mode == self::MODE_RANKING) $this->handleRanking();
		
		if( is_a($this->media,"VoiceInfo") )
		{
			$apiUrl = sprintf("%smedia.php?id=%s",API_URL,$this->media->mediaid);
			$this->assign( 'media_info', $this->media );
			$this->assign( 'api_now', $apiUrl );
		}
	}
	
	function handlePlaylist()
	{
		$playlistInfo = $this->playlistDb->getInfo( $this->memory['pid'] );
		if( !$playlistInfo ) throw new VoiceException(CommonMessages::get()->msg('NO_PLAYLIST_INFO'));

		$index = $this->memory['index'] ? $this->memory['index'] : 0;
		$mid = $playlistInfo->getMediaId( $index );

		$nextMid = $playlistInfo->getMediaId( $index + 1 );
		if( $nextMid )
		{
			$this->memory['index'] = $index + 1;
			$string = urlencode(json_encode($this->memory));
			$this->assign( 'url_next', sprintf("%sjplayer.php?memory=%s",HOME_URL,$string) );
		}

		$this->media = MediaInfo::getInfo( $mid, array('detail'=>true) );
		if( !$this->media->imageid && $playlistInfo->imageid ) $this->media->imageid = $playlistInfo->imageid;
	}
	
	function handleRanking()
	{
		$generator = RankingGenerator::factory( $this->memory['type'] );
		$infos = $generator->get();
		
		$index = $this->memory['index'] ? $this->memory['index'] : 0;
		$mid = $infos[ $index ]->mediaid;

		$nextMid = $infos[ $index+1 ] ? $infos[ $index+1 ]->mediaid : null;
		if( $nextMid )
		{
			$this->memory['index'] = $index + 1;
			$string = urlencode(json_encode($this->memory));
			$this->assign( 'url_next', sprintf("%sjplayer.php?memory=%s",HOME_URL,$string) );
		}
		
		$this->media = $infos[ $index ];
	}
}
$web = new PlayerWeb();
$web->run();
