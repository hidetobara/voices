<?php
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class VoiceFile
{
	const ERROR_NO_AUDIO_FILE = 'This file is not audio';
	
	function __construct( $options=null )
	{
	}
	
	/*
	 * save voice file from temp file
	 * @return path of voice file
	 */
	function save( Array $src, VoiceInfo $info )
	{
		$pathSrc = $src['tmp_name'];
		if( $src['type'] != 'audio/mpeg' ) throw VoiceException(self::ERROR_NO_AUDIO_FILE);
		
		$dirDst = FILE_DIR . "voices/" . $info->uploadTime->format('Y/m-d/');
		if( !file_exists($dirDst) ) mkdir( $dirDst, 0777, true );
		$pathDst = $dirDst . $info->voiceid . ".mp3";
		
		copy( $pathSrc, $pathDst );
		return $pathDst;
	}
	function load( VoiceInfo $info )
	{
		$path = FILE_DIR . "voices/" . $info->uploadTime->format('Y/m-d/') . $info->voiceid . ".mp3";
		return file_get_contents( $path );
	}
}
?>