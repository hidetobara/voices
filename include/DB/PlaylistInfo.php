<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class PlaylistInfo
{
	const MEDIA_MAX = 30;
	
	public $playlistid;
	public $userid;
	public $title;
	public $mediaids;
	public $imageid;
	
	function __construct( $p=null )
	{
		if( is_array($p) ) $this->copyInfo($p);
	}
	function copyInfo( $p )
	{
		if( is_numeric($p['playlist_id']) ) $this->playlistid = (int)$p['playlist_id'];
		if( is_numeric($p['user_id']) ) $this->userid = (int)$p['user_id'];
		if( is_string($p['title']) ) $this->title = strip_tags($p['title']);
		if( $p['media_ids'] && is_string($p['media_ids']) ) $this->mediaids = mb_split(';',$p['media_ids']);
		if( is_array($p['media_ids_array']) ) $this->voiceids = $p['media_ids_array'];
		if( is_numeric($p['image_id']) ) $this->imageid = (int)$p['image_id'];
		
		if( is_array($this->mediaids) ) $this->mediaids = array_slice( $this->mediaids, 0, self::MEDIA_MAX );
	}
	
	function getMediaId( $index )
	{
		if( !is_array($this->mediaids) ) return null;
		return $this->mediaids[ $index ];
	}
	function addMediaId( $vid )
	{
		if( count($this->mediaids) >= self::MEDIA_MAX ) throw new VoiceMessageException('OVER_MEDIA_MAX');
		$this->mediaids[] = $vid;
	}
}

class PlaylistInfoDB extends BaseDB
{
	const TABLE_INFO = 'voices_playlist_info';
	
	function newInfo( PlaylistInfo $info )
	{
		$sql = sprintf( "INSERT INTO %s (`user_id`,`title`,`media_ids`,`image_id`)"
			. " VALUES(:userid,:title,:vids,:imageid)", self::TABLE_INFO );
		$params = array(
			':userid' => $info->userid,
			':title' => $info->title,
			':vids' => is_array($info->mediaids) ? implode(';',$info->mediaids) : "",
			':imageid' => $info->imageid );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$sql = sprintf( "SELECT * FROM %s WHERE `user_id`=:userid ORDER BY `playlist_id` DESC LIMIT 1",
			self::TABLE_INFO );
		$params = array(
			':userid' => $info->userid );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$hash = $state->fetch( PDO::FETCH_ASSOC );
		return new PlaylistInfo( $hash );	
	}
	
	function updateInfo( PlaylistInfo $info )
	{
		$sql = sprintf( "UPDATE %s SET `title`=:title, `media_ids`=:vids, `image_id`=:imageid"
			. " WHERE `playlist_id`=:pid", self::TABLE_INFO );
		$params = array(
			':title' => $info->title,
			':vids' => is_array($info->mediaids) ? implode(';',$info->mediaids) : "",
			':imageid' => $info->imageid,
			':pid' => $info->playlistid );
		$state = $this->pdo->prepare( $sql );
		return $state->execute( $params );
	}
	
	function getInfo( $id )
	{
		$sql = sprintf( "SELECT * FROM %s WHERE `playlist_id`=:pid", self::TABLE_INFO );
		$params = array(
			':pid' => $id );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$hash = $state->fetch( PDO::FETCH_ASSOC );
		if( !$hash ) return null;
		return new PlaylistInfo( $hash );
	}
	
	function getUserInfos( $userid )
	{
		$sql = sprintf( "SELECT * FROM %s WHERE `user_id`=:userid", self::TABLE_INFO );
		$params = array(
			':userid' => $userid );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$list = array();
		while( $hash = $state->fetch( PDO::FETCH_ASSOC ) )
		{
			$list[] = new PlaylistInfo( $hash );
		}
		return $list;
	}
	
	function deleteInfo( PlaylistInfo $info )
	{
		$sql = sprintf( "DELETE FROM %s WHERE `playlist_id`=:pid", self::TABLE_INFO );
		$params = array(
			':pid' => $info->playlistid );
		$state = $this->pdo->prepare( $sql );
		return $state->execute( $params );
	}
}
?>