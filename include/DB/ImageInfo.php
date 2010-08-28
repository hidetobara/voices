<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class ImageInfo
{
	public $imageid;
	public $userid;
	public $uploadTime;
	
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
	}
}

class ImageInfoDB extends BaseDB
{
	const TABLE_INFO = 'voices_image_info';
	
	function newInfo( $userid )
	{
		$now = date('c');
		$sql = sprintf("INSERT INTO %s (`user_id`,`upload_time`) VALUES(:userid,:now)",
			self::TABLE_INFO );
		$params = array(
			':userid' => $userid,
			':now' => $now );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;

		$sql = sprintf("SELECT * FROM %s WHERE `user_id`=:userid AND `upload_time`=:now LIMIT 1",
			self::TABLE_INFO );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$hash = $state->fetch( PDO::FETCH_ASSOC );
		return new ImageInfo( $hash );
	}
	
	function getInfos( $userid )
	{
		$params = array(
			':userid' => $info->userid );
		$sql = sprintf("SELECT * FROM %s WHERE `user_id`=:userid",
			self::TABLE_INFO );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;

		$infos = array();
		while( $hash = $state->fetch(PDO::FETCH_ASSOC) )
		{
			$infos[] = new ImageInfo( $hash );	
		}
		return new $infos;
	}
}
