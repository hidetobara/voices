<?php
require_once( INCLUDE_DIR . "program/RecentRankingProgram.php" );
require_once( INCLUDE_DIR . "program/PlaylistProgram.php" );
require_once( INCLUDE_DIR . "program/RandomPlayProgram.php" );


class ProgramHandler
{
	static function handleMemory( $userid, $mem )
	{
		switch( $mem['program'] )
		{
			case RecentRankingProgram::NAME:
				return new RecentRankingProgram( $userid, $mem );
			case PlaylistProgram::NAME:
				return new PlaylistProgram( $userid, $mem );
			case RandomPlayProgram::NAME:
				return new RandomPlayProgram( $userid, $mem );
		}
		return null;
	}
}
?>
