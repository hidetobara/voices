<?php
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );
require_once( INCLUDE_DIR . "DB/ImageResourceInfo.php" );


function smarty_function_image_link($params, &$smarty)
{
	$voiceInfo = $params['voice_info'];
	if( is_a($voiceInfo,'VoiceInfo') ) $imageid = $voiceInfo->imageid;
	$imageInfo = $params['image_info'];
	if( is_a($imageInfo,'ImageInfo') ) $imageid = $imageInfo->imageid;
	$name = $params['size'];
	$size = ImageResourceInfo::name2size( $name );
	
	if( !$imageid || !$size )
	{
		if( !$size ) $size = 64;
		print "<img height={$size} width={$size}>";
		return;
	}
	
	//printf( "<div style=\"height: %d; width: %d;\">", $size, $size );
	printf( "<img src=\"%simage.php?id=%d&size=%s\">", API_URL, $imageid, $name );
	//printf( "</div>" );
}

?>
