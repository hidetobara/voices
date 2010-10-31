<?php
require_once( INCLUDE_DIR . "program/BaseProgram.php" );


class RecentRankingProgram extends BaseProgram
{
	const NAME = 'RecentRanking';
	
	public $index;
	protected $infos;	///// for cache
	
	public $cacher;	
	protected $voiceDb;
	
	const KEY_CACHE = 'program_recent_ranking';
	const SELECT_LIMIT = 1000;
	const SHOW_LIMIT = 10;
	
	function __construct( $userid, $mem, $opt=null )
	{
		parent::__construct( $userid, $mem );
		$this->index = $mem['index'] ? intval($mem['index']) : 0;		
		
		$this->cacher = $opt['Cache'] ? $opt['Cache'] : new FileCache();
		$this->voiceDb = $opt['VoiceInfoDB'] ? $opt['VoiceInfoDB'] : new VoiceInfoDB();
	}
	
	function getInfos()
	{
		if( $this->infos ) return $this->infos;
		
		$infos = $this->loadCache();
		if( $infos ) return $infos;
		
		$infos = $this->calcInfos();
		$this->saveCache( $infos );
		$this->infos = $infos;
		return $infos;
	}
	
	protected function loadCache()
	{	
		$array = $this->cacher->get( self::KEY_CACHE );		
		if( !$array ) return null;

		$infos = array();
		foreach( $array as $hash ) $infos[] = new VoiceInfo($hash);
		return $infos;
	}
	protected function calcInfos()
	{
		$list = $this->voiceDb->getListByRecentPlaying( self::SELECT_LIMIT );
		arsort( $list );
	
		$infos = array();
		foreach( $list as $vid => $count )
		{
			$info = $this->voiceDb->getInfo( $vid );
			if( $count < 0 ) continue;
			if( !$info->isVisible ) continue;
			
			$infos[] = $info;
			if( count($infos) >= self::SHOW_LIMIT ) break;
		}		
		return $infos;
	}
	protected function saveCache( $infos )
	{
		$array = array();
		foreach( $infos as $info ) $array[] = $info->toArray();
		$expire = new DateTime("+1 day");
		$expire->setTime( 5, 0, 0 );
		$this->cacher->set( self::KEY_CACHE, $array, $expire );
	}
	
	function currentInfo()
	{
		$infos = $this->getInfos();
		$info = $infos[ $this->index ];
		if( !$info ) return null;
		
		$this->voiceDb->getDetail($info);
		return $info;
	}
	function nextInfo()
	{
		$infos = $this->getInfos();
		return $infos[ $this->index+1 ];
	}
}
?>