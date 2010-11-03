<?php
require_once( INCLUDE_DIR . "program/BasePlayProgram.php" );


class RecentRankingProgram extends BasePlayProgram
{
	const NAME = 'RecentRanking';
	const SELECT_LIMIT = 1000;
	const SHOW_LIMIT = 10;
	
	protected function getCacheKey()
	{
		return 'program_recent_ranking';
	}
	protected function getExpireDate()
	{
		$date = new DateTime( "+1 day" );
		$date->setTime( 5, 0, 0 );
		return $date;
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
			if( !$info->checkVisible() ) continue;
			
			$infos[] = $info;
			if( count($infos) >= self::SHOW_LIMIT ) break;
		}		
		return $infos;
	}
}
?>