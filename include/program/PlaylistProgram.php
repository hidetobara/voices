<?php
require_once( INCLUDE_DIR . "program/BaseProgram.php" );


class PlaylistProgram extends BaseProgram
{
	const NAME = 'Playlist';
	
	public $index;
	protected $pid;
	protected $infos;	///// for cache
	
	protected $playlistDb;
	protected $playlistInfo;
	
	const SELECT_LIMIT = 1000;
	const SHOW_LIMIT = 10;
	
	function __construct( $userid, $mem )
	{
		parent::__construct( $userid, $mem );
		$this->index = $mem['index'] ? intval($mem['index']) : 0;
		$this->pid = $mem['pid'];
		
		$this->playlistDb = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
	}
	
	function getInfos()
	{
		$playlistInfo = $this->getPlaylistInfo();
		if( !is_array($playlistInfo->mediaids) ) return array();
		
		$infos = array();
		foreach( $playlistInfo->mediaids as $mid )
		{
			$infos[] = MediaInfo::getInfo($mid);
		}
		return $infos;
	}
	private function getPlaylistInfo()
	{
		if( $this->playlistInfo ) return $this->playlistInfo;
		
		if( !is_numeric($this->pid) ) throw new VoiceException(CommonMessages::get()->msg('INVALID_PARAMETER'));
		$playlistInfo = $this->playlistDb->getInfo( $this->pid );
		if( !$playlistInfo ) throw new VoiceException(CommonMessages::get()->msg('NO_PLAYLIST_INFO'));
		$this->playlistInfo = $playlistInfo;
		return $this->playlistInfo;
	}

	function previousInfo()
	{
		$playlistInfo = $this->getPlaylistInfo();
		$mid = $playlistInfo->getMediaId( $this->index-1 );
		return MediaInfo::getInfo($mid);
	}
	function currentInfo()
	{
		$playlistInfo = $this->getPlaylistInfo();
		$mid = $playlistInfo->getMediaId( $this->index );
		$info = MediaInfo::getInfo($mid,array('detail'=>true));
		if( !$info ) return null;
		
		if( !$info->imageid && $playlistInfo->imageid ) $info->imageid = $playlistInfo->imageid;
		return $info;
	}
	function nextInfo()
	{
		$playlistInfo = $this->getPlaylistInfo();
		$mid = $playlistInfo->getMediaId( $this->index+1 );
		return MediaInfo::getInfo($mid);
	}
}

?>