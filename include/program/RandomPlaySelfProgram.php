<?php
require_once( INCLUDE_DIR . "program/BasePlayProgram.php" );


class RandomPlaySelfProgram extends BasePlayProgram
{
	const NAME = 'RandomPlaySelf';
	
	protected function getCacheKey()
	{
		return sprintf( "program_random_play_self_%d", $this->userid );
	}
	
	protected function calcInfos()
	{
		$list = $this->voiceDb->getInfosByUser( $this->userid );
		
		$infos = array();
		foreach( $list as $info )
		{
			if( !$info->checkVisible() ) continue;
			$infos[] = $info;
		}
		shuffle( $infos );	
		return $infos;
	}
}