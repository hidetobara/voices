<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "DB/MediaInfo.php" );

class MedialistWeb extends BaseWeb
{	
	protected $play;
	protected $playDb;

	protected $mid;
	protected $command;
	protected $index;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'medialist';
		$this->template = 'medialist.tpl';
		
		$this->playDb = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();
		
		$this->play = new PlaylistInfo( $_REQUEST );
		if( $this->play->playlistid )
		{
			$this->play = $this->playDb->getInfo( $this->play->playlistid );
		}

		$this->mid = $_REQUEST[ 'media_id' ];
		$this->command = $_REQUEST[ 'command' ];
		$this->index = $_REQUEST[ 'index' ];
		if( !is_string($this->mid) ) $this->mid = null;
		if( !is_string($this->command) ) $this->command = null;
		if( !is_numeric($this->index) ) $this->index = null;
		
		$this->assign( 'playlist_info', $this->play );
	}
	
	function handle()
	{
		$medias = $this->getMedias();

		if( $this->command )
		{
			$this->checkOwnPlaylist( $this->play );
			
			switch( $this->command )
			{
				case 'up':
					$this->upMedia( $medias, $this->index );
					break;
				case 'down':
					$this->downMedia( $medias, $this->index );
					break;
					
				case 'add':
					$this->addMedia( $medias, $this->mid );
					break;
				case 'delete':
					$this->deleteMedia( $medias, $this->index );
					break;
			}
		}

		$this->assign( 'media_array', $medias );
	}
	
	function getMedias()
	{
		if( !$this->play ) throw new VoiceMessageException('NO_PLAYLIST');

		$voices = array();
		if( count($this->play->mediaids) == 0 ) return $voices;
		
		foreach( $this->play->mediaids as $vid )
		{
			$info = MediaInfo::getInfo($vid, array('detail'=>true));
			if( $info ) $voices[] = $info;
		}
		return $voices;		
	}

	function upMedia( &$medias, $index )
	{
		$previous = $index - 1;
		if( $previous < 0 ) return;
		
		$tmp = $medias[ $previous ];
		$medias[ $previous ] = $medias[ $index ];
		$medias[ $index ] = $tmp;
		
		$this->play->mediaids = $this->getMediaids($medias);
		$this->playDb->updateInfo( $this->play );
	}
	
	function downMedia( &$medias, $index )
	{
		$next = $index + 1;
		if( $next >= count($medias) ) return;
		
		$tmp = $medias[ $next ];
		$medias[ $next ] = $medias[ $index ];
		$medias[ $index ] = $tmp;
		
		$this->play->mediaids = $this->getMediaids($medias);
		$this->playDb->updateInfo( $this->play );
	}
	
	function addMedia( &$medias, $vid )
	{
		if( !$vid ) throw new VoiceMessageException('NO_MEDIA_INFO');

		$info = MediaInfo::getInfo($vid);
		if( !$info ) return;
		
		$medias[] = $info;
		
		$this->play->mediaids = $this->getMediaids($medias);
		$this->playDb->updateInfo( $this->play );
	}
	
	function deleteMedia( &$medias, $index )
	{
		if( !is_numeric($index) ) throw new VoiceMessageException('NO_MEDIA_INFO');
		
		unset( $medias[ $index ] );
		
		$this->play->mediaids = $this->getMediaids($medias);
		$this->playDb->updateInfo( $this->play );
	}
		
	function getMediaids( $medias )
	{
		$keys = array();
		foreach( $medias as $info ) $keys[] = $info->mediaid;
		return $keys;
	}
	
	function checkOwnPlaylist( $play )
	{
		if( !$play ) return;
		if( $play->userid != $this->userid ) throw new VoiceMessageException('INVALID_PARAMETER');
	}
}
$web = new MedialistWeb();
$web->run();
?>