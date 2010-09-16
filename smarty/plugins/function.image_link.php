<?php
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );
require_once( INCLUDE_DIR . "DB/ImageResourceInfo.php" );


function smarty_function_image_link($params, &$smarty)
{
	$playlistInfo = $params['_playlist_info'];
	if( is_a($playlistInfo,'PlaylistInfo') ) $imageid = $playlistInfo->imageid;

	$mediaInfo = $params['_media_info'];
	if( is_a($mediaInfo,'MediaInfo') ) $imageid = $mediaInfo->imageid;
	
	$imageInfo = $params['_image_info'];
	if( is_a($imageInfo,'ImageInfo') ) $imageid = $imageInfo->imageid;
	
	if( $params['_info'] )
	{
		$info = $params['_info'];
		$imageid = $info->imageid;
	}
	
	$name = $params['size'];
	$size = ImageResourceInfo::name2size( $name );
	
	if( !$imageid || !$size )
	{
		if( !$size ) $size = 64;
		print "<img height='{$size}' width='{$size}' border='1'>";
		return;
	}
	
	//printf( "<div style=\"height: %d; width: %d;\">", $size, $size );
	printf( "<img src=\"%simage.php?id=%d&size=%s\">", API_URL, $imageid, $name );
	//printf( "</div>" );
}

?>
