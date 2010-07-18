<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class PlaylistInfo
{
	const VOICE_MAX = 30;
	
	public $playlistid;
	public $userid;
	public $title;
	public $voiceids;
	public $thumbnailid;
	
	function __construct( $p=null )
	{
		if( is_array($p) ) $this->copyInfo($p);
	}
	function copyInfo( $p )
	{
		if( is_numeric($p['playlist_id']) ) $this->playlistid = (int)$p['playlist_id'];
		if( is_numeric($p['user_id']) ) $this->userid = (int)$p['user_id'];
		if( is_string($p['title']) ) $this->title = $p['title'];
		if( $p['voice_ids'] && is_string($p['voice_ids']) ) $this->voiceids = mb_split(';',$p['voice_ids']);
		if( is_array($p['voice_ids_array']) ) $this->voiceids = $p['voice_ids_array'];
		if( is_numeric($p['thumbnail_id']) ) $this->thumbnailid = (int)$p['thumbnail_id'];
		
		if( count($this->voiceids) >= self::VOICE_MAX ) $this->voiceids = array_slice( $this->voiceids, 0, self::VOICE_MAX );
	}
}

class PlaylistInfoDB extends BaseDB
{
	const TABLE_INFO = 'playlist_info';
	
	function newInfo( PlaylistInfo $info )
	{
		$sql = sprintf( "INSERT INTO %s (`user_id`,`title`,`voice_ids`,`thumbnail_id`)"
			. " VALUES(:userid,:title,:vids,:thumbid)", self::TABLE_INFO );
		$params = array(
			':userid' => $info->userid,
			':title' => $info->title,
			':vids' => is_array($info->voiceids) ? implode(',',$info->voiceids) : "",
			':thumbid' => $info->thumbnailid );
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
		$sql = sprintf( "UPDATE %s SET `title`=:title, `voice_ids`=:vids, `thumbnail_id`=:thumbid"
			. " WHERE `playlist_id`=:pid", self::TABLE_INFO );
		$params = array(
			':title' => $info->title,
			':vids' => is_array($info->voiceids) ? implode(';',$info->voiceids) : "",
			':thumbid' => $info->thumbnailid,
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