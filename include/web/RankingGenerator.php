<?php
require_once( INCLUDE_DIR . "DB/MediaInfo.php" );
require_once( INCLUDE_DIR . "File/FileCache.php" );

class RankingGenerator
{
	static function factory( $type )
	{
		switch( $type )
		{
			case 'recent':
				return new RankingGeneratorRecent();
		}
		return null;
	}
}

class RankingGeneratorRecent extends RankingGenerator
{
	const KEY_CACHE = 'ranking_recent';
	const SELECT_LIMIT = 100;
	const SHOW_LIMIT = 10;
	
	function get()
	{
		$cacher = new FileCache();
		$array = $cacher->get( self::KEY_CACHE );
		
		///// cache exists !
		if( $array )
		{
			$infos = array();
			foreach( $array as $hash ) $infos[] = new VoiceInfo($hash);
			return $infos;
		}
		
		///// no cache
		$voiceDb = new VoiceInfoDB();
		$list = $voiceDb->getListByRecent( self::SELECT_LIMIT );
		arsort( $list );
	
		$infos = array();
		foreach( $list as $vid => $count )
		{
			$info = $voiceDb->getInfo( $vid );
			if( !$info->isVisible ) continue;
			$voiceDb->getDetail($info);
			$voiceDb->getPlaying($info);
			$infos[] = $info;
			if( count($infos) >= self::SHOW_LIMIT ) break;
		}
		
		$array = array();
		foreach( $infos as $info ) $array[] = $info->toArray();
		$expire = new DateTime("+1 day");
		$expire->setTime( 5, 0, 0 );
		$cacher->set( self::KEY_CACHE, $array, $expire );
		
		return $infos;
	}
}
