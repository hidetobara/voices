<?php
require_once( INCLUDE_DIR . "program/BaseProgram.php" );


abstract class BasePlayProgram extends BaseProgram
{
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
	
	abstract protected function getCacheKey();
	protected function getExpireDate()
	{
		$date = new DateTime("+3 hour");
		return $date;
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
		$array = $this->cacher->get( $this->getCacheKey() );
		if( !$array ) return null;

		$infos = array();
		foreach( $array as $hash ) $infos[] = new VoiceInfo($hash);
		return $infos;
	}

	abstract protected function calcInfos();
	
	protected function saveCache( $infos )
	{
		$array = array();
		foreach( $infos as $info ) $array[] = $info->toArray();
		$this->cacher->set( $this->getCacheKey(), $array, $this->getExpireDate() );
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