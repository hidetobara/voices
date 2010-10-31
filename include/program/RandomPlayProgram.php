<?php
require_once( INCLUDE_DIR . "program/BaseProgram.php" );


class RandomPlayProgram extends BaseProgram
{
	const NAME = 'RandomPlay';
	const KEY_CACHE = 'program_random_play_all';
	const SELECT_LIMIT = 1000;
	
	protected $cacher;
	protected $voiceDb;
	
	public $index;
	protected $infos;	///// for cache
	
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
		//$array = $this->cacher->get( self::KEY_CACHE );		
		if( !$array ) return null;

		$infos = array();
		foreach( $array as $hash ) $infos[] = new VoiceInfo($hash);
		return $infos;
	}
	protected function calcInfos()
	{
		$list = $this->voiceDb->getListByRecentRegistered( self::SELECT_LIMIT );
		shuffle( $list );
	
		$infos = array();
		foreach( $list as $vid )
		{
			$info = $this->voiceDb->getInfo( $vid );
			if( !$info || !$info->isVisible ) continue;
			$infos[] = $info;
		}
		return $infos;
	}
	protected function saveCache( $infos )
	{
		$array = array();
		foreach( $infos as $info ) $array[] = $info->toArray();
		$expire = new DateTime("+3 hour");	///// 3hour !
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