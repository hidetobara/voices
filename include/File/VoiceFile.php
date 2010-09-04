<?php
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class VoiceFile
{	
	private $validContentTypes = array(
		'audio/mp3' => 'mp3',
		'audio/mpeg' => 'mp3' );
	
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
		$type = $this->validContentTypes[ $src['type'] ];
		if( !$type ) throw new VoiceException(CommonMessages::get()->msg('NOT_AUDIO_FILE'),$src);
		
		$dirDst = VOICE_DIR . $info->uploadTime->format('Y/m-d/');
		if( !file_exists($dirDst) ) mkdir( $dirDst, 0777, true );
		$pathDst = $dirDst . $info->voiceid . ".mp3";
		
		copy( $pathSrc, $pathDst );
		return $pathDst;
	}
	
	function delete( VoiceInfo $info )
	{
		unlink( $info->dst );
	}
}
?>