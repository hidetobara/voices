<?php
require_once( INCLUDE_DIR . "program/RecentRankingProgram.php" );
require_once( INCLUDE_DIR . "program/PlaylistProgram.php" );
require_once( INCLUDE_DIR . "program/RandomPlayAllProgram.php" );
require_once( INCLUDE_DIR . "program/RandomPlaySelfProgram.php" );


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
			case RandomPlayAllProgram::NAME:
				return new RandomPlayAllProgram( $userid, $mem );
			case RandomPlaySelfProgram::NAME:
				return new RandomPlaySelfProgram( $userid, $mem );
		}
		return null;
	}
}
?>
