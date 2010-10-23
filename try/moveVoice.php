<?php
require_once( '../configure.php' );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class MoveVoiceProcess
{
	function run( $userid )
	{
		foreach( $this->getInfos($userid) as $info )
		{
			$this->copyFile($info);
			$this->updateRecord($info);
		}
	}
	
	function getInfos( $userid )
	{
		$db = new VoiceInfoDB();
		return $db->getInfosByUser( $userid );
	}
	function copyFile( $info )
	{
		$to = $this->newPath( $info );
		$dir = dirname($to);
		mkdir( $dir, 0777, true );
		$from = $info->dst;
		rename( $from, $to );
	}
	function updateRecord( $info )
	{
		$db = new VoiceInfoDB();
		$info->dst = $this->newPath( $info );
		$db->updateInfo( $info );
	}
	
	function newPath( $info )
	{
		return sprintf( "%suser%d/%d.mp3", VOICE_DIR, $info->userid, $info->voiceid );
	}
}
$p = new MoveVoiceProcess();
for( $i=1000; $i<1020; $i++ ) $p->run( $i );
?>