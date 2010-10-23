<?php
require_once( '../configure.php' );
require_once( INCLUDE_DIR . "DB/ImageResourceInfo.php" );
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );


class MoveImageProcess
{
	function run( $userid )
	{
		foreach( $this->getInfos($userid) as $info )
		{
			$this->copyFile($info);
		}
	}
	
	function getInfos( $userid )
	{
		$db = new ImageInfoDB();
		return $db->getInfos( $userid );
	}
	function copyFile( $info )
	{
		$db = new ImageResourceInfoDB();
		foreach( array(64,320) as $size )
		{
			$rsc = $db->getInfo( $info->imageid, $size );
			if( !$rsc || !$rsc->dst ) continue;
			$from = $rsc->dst;
			if( !file_exists($from) ) continue;
			$to = sprintf( "%suser%d/%d_%d.%s", IMAGE_DIR, $info->userid, $info->imageid, $size, $rsc->type );
			$dir = dirname($to);
			if( !is_dir($dir) ) mkdir($dir, 0777, true);
			copy( $from, $to );
		}
	}	
}
$p = new MoveImageProcess();
$p->run( 1000 );
#$p->run( 1005 );
#$p->run( 1007 );
#$p->run( 1009 );
?>
