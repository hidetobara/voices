<?php
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class MediaInfo
{
	public $type = null;
	const MEDIA_VOICE = 'voice';
	
	public $isVisible = false;
	public $mediaid;
	public $imageid;
	
	static function getInfo($mid, $opt=null)
	{
		///// Voice
		if( preg_match( "/v([\d]+)/", $mid, $matches ) )
		{
			$id = intval($matches[1]);
			$voiceDb = new VoiceInfoDB();
			$info = $voiceDb->getInfo($id);
			if( $info && $opt['detail'] ) $voiceDb->getDetail($info);
			if( $info && $opt['playing']) $voiceDb->getPlaying($info);
			return $info;
		}
		return null;
	}
}
?>