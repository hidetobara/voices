<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "DB/MediaInfo.php" );

class VoicelistWeb extends BaseWeb
{	
	protected $mid;
	protected $play;
	protected $playDb;
	
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
		
		$this->assign( 'playlist_info', $this->play );
	}
	
	function handle()
	{
		$medias = $this->getMedias();
		$index = $_REQUEST['index'];

		switch( $_REQUEST[ 'command' ] )
		{
			case 'add':
				$this->addMedia( $medias, $this->mid );
				break;
			case 'delete':
				$this->deleteMedia( $medias, $index );
				break;
		}

		$this->assign( 'media_array', $medias );
	}
	
	function getMedias()
	{
		if( !$this->play ) throw new VoiceException(CommonMessages::get()->msg('NO_PLAYLIST'));

		$voices = array();
		if( count($this->play->mediaids) == 0 ) return $voices;
		
		foreach( $this->play->mediaids as $vid )
		{
			$info = MediaInfo::getInfo($vid, array('detail'=>true));
			if( $info ) $voices[] = $info;
		}
		return $voices;		
	}
	
	function addMedia( &$medias, $vid )
	{
		if( !$vid ) throw new VoiceException(CommonMessages::get()->msg('NO_MEDIA_INFO'));

		$info = MediaInfo::getInfo($vid, array('detail'=>true));
		$medias[] = $info;
		$this->play->mediaids = $this->getMediaids($medias);
		$this->playDb->updateInfo( $this->play );
	}
	
	function deleteMedia( &$medias, $index )
	{
		if( !is_numeric($index) ) throw new VoiceException(CommonMessages::get()->msg('NO_MEDIA_INFO'));
		
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
}
$web = new VoicelistWeb();
$web->run();
?>