<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class ImageResourceInfo
{
	const ICON_SIZE = 64;
	const WALL_SIZE = 320;
	
	public $resourceid;
	public $imageid;
	public $type;
	public $size;
	public $dst;
	
	function __construct( $p=null )
	{
		if( is_array($p) )
		{
			$this->copyInfo( $p );
		}
	}
	function copyInfo( Array $p )
	{
		if(is_numeric($p['resource_id'])) $this->thumbnailid = intval($p['resource_id']);
		if(is_numeric($p['image_id'])) $this->imageid = intval($p['image_id']);
		if(is_string($p['type'])) $this->type = $p['type'];
		if(is_numeric($p['size'])) $this->size = intval($p['size']);
		if($p['dst']) $this->dst = $p['dst'];
	}
	
	static function name2size( $name )
	{
		switch( $name )
		{
			case 'icon':	return self::ICON_SIZE;
			case 'wall':	return self::WALL_SIZE;
		}
		return null;
	}
}

class ImageResourceInfoDB extends BaseDB
{
	const TABLE_INFO = 'voices_image_resource_info';
	
	function newInfo( ImageResourceInfo $info )
	{
		$sql = sprintf("INSERT INTO %s (`image_id`,`type`,`size`,`dst`) VALUES(:imageid,:type,:size,:dst)",
			self::TABLE_INFO );
		$params = array(
			':imageid' => $info->imageid,
			':type' => $info->type,
			':size' => $info->size,
			':dst' => $info->dst );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;

		return $this->getInfo( $info->imageid, $info->size );
	}
	
	function getInfo( $imageid, $size )
	{
		$params = array(
			':imageid' => $imageid,
			':size' => $size );
		$sql = sprintf("SELECT * FROM %s WHERE `image_id`=:imageid AND `size`=:size LIMIT 1",
			self::TABLE_INFO );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$hash = $state->fetch( PDO::FETCH_ASSOC );
		return new ImageResourceInfo( $hash );		
	}
}