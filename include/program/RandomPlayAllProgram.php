<?php
require_once( INCLUDE_DIR . "program/BasePlayProgram.php" );


class RandomPlayAllProgram extends BasePlayProgram
{
	const NAME = 'RandomPlayAll';
	const SELECT_LIMIT = 1000;
	
	protected function getCacheKey()
	{
		return "program_random_play_all";
	}
	
	protected function calcInfos()
	{
		$list = $this->voiceDb->getListByRecentRegistered( self::SELECT_LIMIT );
		shuffle( $list );
	
		$infos = array();
		foreach( $list as $vid )
		{
			$info = $this->voiceDb->getInfo( $vid );
			if( !$info || !$info->checkVisible() ) continue;
			$infos[] = $info;
		}
		return $infos;
	}
}