<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class ImageInfo
{
	const ICON_SIZE = 64;
	const WALL_SIZE = 320;
	
	public $imageid;
	public $userid;
	public $uploadTime;
	public $type;
	
	function __construct( $p=null )
	{
		if( is_array($p) )
		{
			$this->copyInfo( $p );
		}
	}
	function copyInfo( Array $p )
	{
		if(is_numeric($p['image_id'])) $this->imageid = intval($p['image_id']);
		if(is_numeric($p['user_id'])) $this->userid = intval($p['user_id']);
		if(is_string($p['upload_time'])) $this->uploadTime = new DateTime($p['upload_time']);
		if(is_string($p['type'])) $this->type = $p['type'];
	}

	function getFilePath( $size )
	{
		return sprintf( "%suser%d/%d_%d.%s", IMAGE_DIR, $this->userid, $this->imageid, $size, $this->type );
	}
	
///// static
	static function isValidType( $type )
	{
		switch($type)
		{
			case 'jpeg':
			case 'jpg':
				return 'jpg';
			case 'png':
				return 'png';
		}
		return null;
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

class ImageInfoDB extends BaseDB
{
	const TABLE_INFO = 'voices_image_info';
	
	function newInfo( ImageInfo $info )
	{
		if( !$info->userid || !ImageInfo::isValidType( $info->type ) ) return null;
		
		$now = date('c');
		$sql = sprintf("INSERT INTO %s (`user_id`,`upload_time`,`type`) VALUES(:userid,:now,:type)",
			self::TABLE_INFO );
		$params = array(
			':userid' => $info->userid,
			':now' => $now,
			':type' => $info->type );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;

		$sql = sprintf("SELECT * FROM %s WHERE `user_id`=:userid AND `upload_time`=:now LIMIT 1",
			self::TABLE_INFO );
		unset($params[':type']);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$hash = $state->fetch( PDO::FETCH_ASSOC );
		return new ImageInfo( $hash );
	}
	
	function getInfo( $imageid )
	{
		$params = array(
			':imageid' => $imageid );
		$sql = sprintf("SELECT * FROM %s WHERE `image_id`=:imageid",
			self::TABLE_INFO );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;

		$hash = $state->fetch(PDO::FETCH_ASSOC);
		if( !$hash ) return null;
		return new ImageInfo( $hash );
	}
	
	function getInfos( $userid )
	{
		$params = array(
			':userid' => $userid );
		$sql = sprintf("SELECT * FROM %s WHERE `user_id`=:userid",
			self::TABLE_INFO );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;

		$infos = array();
		while( $hash = $state->fetch(PDO::FETCH_ASSOC) )
		{
			if( !$hash ) continue;
			$infos[] = new ImageInfo( $hash );
		}
		return $infos;
	}
}
