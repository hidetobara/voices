<?php
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "DB/BaseDB.php" );
require_once( INCLUDE_DIR . "DB/MediaInfo.php" );


class VoiceInfo extends MediaInfo
{
	const TITLE_LENGTH_MAX = 64;
	const ARTIST_LENGTH_MAX = 64;
	const DESCRIPTION_LENGTH_MAX = 256;
	const TAG_LENGTH_MAX = 16;
	const TAGS_MAX = 10;
	
	///// No change
	public $voiceid;
	public $userid;
	public $dst;
	public $playable;
	public $uploadTime;
	public $sizeKb;
	
	///// Maybe change
	public $title;
	public $artist;
	public $description;
	public $tags;

	///// always change
	public $playedCount;
	
	function __construct( $p=null )
	{
		$this->type = self::MEDIA_VOICE;
		
		if( is_array($p) )
		{
			$this->copyInfo( $p );
			$this->copyDetail( $p );
			$this->copyPlaying( $p );
			$this->isVisible = true;
		}
	}
	function copyInfo( Array $p )
	{
		if(is_numeric($p['voice_id'])){
			$this->voiceid = intval($p['voice_id']);
			$this->mediaid = "v" . $this->voiceid;
		}
		if(is_numeric($p['user_id'])) $this->userid = intval($p['user_id']);
		if($p['dst']) $this->dst = $p['dst'];
		if(is_string($p['upload_time'])) $this->uploadTime = new DateTime($p['upload_time']);
		if(is_numeric($p['size_kb'])) $this->sizeKb = intval($p['size_kb']);
	}
	function copyDetail( Array $p )
	{
		if(is_numeric($p['image_id'])) $this->imageid = intval($p['image_id']);
		if($p['title']) $this->title = strip_tags($p['title']);
		if($p['artist']) $this->artist = strip_tags($p['artist']);
		if($p['description']) $this->description = htmlspecialchars($p['description']);
		if(is_string($p['tags']))
		{
			$this->tags = mb_split(' ',strip_tags($p['tags']));
			array_slice($this->tags, 0, self::TAGS_MAX);
		}
	}
	function copyPlaying( $p )
	{
		if( is_numeric($p['played_count']) ) $this->playedCount = intval($p['played_count']);
	}
	
	function checkDetail()
	{
		if( mb_strlen($this->title) > self::TITLE_LENGTH_MAX )
			throw new VoiceException("Too long title.");
		if( mb_strlen($this->artist) > self::TITLE_LENGTH_MAX )
			throw new VoiceException("Too long artist name.");
		if( mb_strlen($this->description) > self::DESCRIPTION_LENGTH_MAX )
			throw new VoiceException("Too long description.");
		if( is_array($this->tags) )
		{
			foreach( $this->tags as $tag )
			{
				if( mb_strlen($tag) > self::TAG_LENGTH_MAX ) throw new VoiceException("Too long tag.");
			}
		}
	}
	
	function checkVisible()
	{
		if( !is_file($this->dst) ) return false;
		return true;
	}
	
	function toArray()
	{
		$array = array(
			'voice_id' => $this->voiceid,
			'user_id' => $this->userid,
			'dst' => $this->dst,
			'image_id' => $this->imageid,
			'title' => $this->title,
			'artist' => $this->artist,
			'description' => $this->description,
			'played_count' => $this->playedCount );
		return $array;
	}
}

class VoiceInfoDB extends BaseDB
{
	const TABLE_INFO = 'voices_voice_info';
	const TABLE_DETAIL = 'voices_voice_detail';
	const TABLE_PLAYING = 'voices_voice_playing';
	
	function newInfo( $userid )
	{
		$now = date('c');
		$sql = sprintf("INSERT INTO %s (`user_id`,`upload_time`) VALUES(:userid,:now)",
			self::TABLE_INFO);
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
		
		$sql = sprintf("INSERT INTO %s (`voice_id`) VALUES(:vid)",
			self::TABLE_DETAIL);
		$params = array( ':vid' => $hash['voice_id'] );		
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		return new VoiceInfo( $hash );
	}
	
	function updateInfo( VoiceInfo $info )
	{	///// info
		$sql = sprintf("UPDATE %s SET `dst`=:dst, `size_kb`=:size WHERE `voice_id`=:vid",
			self::TABLE_INFO );
		$params = array(
			':dst' => $info->dst,
			':size' => $info->sizeKb,
			':vid' => $info->voiceid );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return false;
		return true;	
	}
	function updateDetail( VoiceInfo $info )
	{
		$sql = sprintf( "UPDATE %s SET `image_id`=:imageid,`title`=:title,`artist`=:art,`description`=:desc,`tags`=:tags WHERE `voice_id`=:vid",
			self::TABLE_DETAIL );
		$params = array(
			':imageid' => $info->imageid ? $info->imageid : 0,
			':title' => $info->title,
			':art' => $info->artist,
			':desc' => $info->description,
			':tags' => is_array($info->tags) ? implode(' ',$info->tags) : '',
			':vid' => $info->voiceid );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return false;
		return true;
	}
	function updatePlaying( VoiceInfo $info )
	{	///// info
		$sql = sprintf("UPDATE %s SET `played_count`=:count WHERE `voice_id`=:vid",
			self::TABLE_PLAYING );
		$params = array(
			':count' => $info->playedCount,
			':vid' => $info->voiceid );
		$state = $this->pdo->prepare( $sql );
		$state->execute( $params );
		if( $state->rowCount() != 0 ) return true;
		
		$sql = sprintf("INSERT INTO %s (`voice_id`,`played_count`) VALUES(:vid,:count)",
			self::TABLE_PLAYING );
		$state = $this->pdo->prepare( $sql );
		if( $state->execute( $params ) ) return true;
		return false;
	}
	
	function getInfo( $vid )
	{
		if( !preg_match( "/v?([\d]+)/", $vid, $matches ) ) return null;
		$id = intval($matches[1]);
		if( !$id ) return null;
		
		$params = array(
			':vid' => $id );
		$sql = sprintf("SELECT * FROM %s WHERE `voice_id`=:vid LIMIT 1",
			self::TABLE_INFO);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$info = $state->fetch( PDO::FETCH_ASSOC );
		return new VoiceInfo( $info );		
	}
	function getDetail( VoiceInfo $i )
	{
		if( !$i->voiceid || !$i->isVisible ) return $i;
		
		$params = array(
			':vid' => $i->voiceid );
		$sql = sprintf("SELECT * FROM %s WHERE `voice_id`=:vid LIMIT 1",
			self::TABLE_DETAIL);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return $i;
		
		$detail = $state->fetch( PDO::FETCH_ASSOC );
		$i->copyDetail( $detail );
		return $i;
	}
	function getPlaying( VoiceInfo $i )
	{
		if( !$i->voiceid || !$i->isVisible ) return $i;
	
		$params = array(
			':vid' => $i->voiceid );
		$sql = sprintf("SELECT * FROM %s WHERE `voice_id`=:vid LIMIT 1",
			self::TABLE_PLAYING);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return $i;
		
		$array = $state->fetch( PDO::FETCH_ASSOC );
		$i->copyPlaying( $array );
		return $i;
	}
	
	function getInfosByUser( $userid )
	{
		$params = array(
			':userid' => $userid );
		$sql = sprintf( "SELECT * FROM %s WHERE `user_id`=:userid",
			self::TABLE_INFO );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return array();
	
		$infos = array();
		while( true )
		{
			$hash = $state->fetch( PDO::FETCH_ASSOC );
			if( !$hash ) break;
			$infos[] = new VoiceInfo( $hash );
		}
		return $infos;
	}
	function getListByRecentPlaying( $limit=100 )
	{
		$sql = sprintf( "SELECT * FROM %s WHERE 1 ORDER BY `voice_id` DESC LIMIT 0, %d",
			self::TABLE_PLAYING, is_numeric($limit) ? $limit : 100 );
		$state = $this->pdo->query( $sql );

		$list = array();
		while( true )
		{
			$hash = $state->fetch( PDO::FETCH_ASSOC );
			if( !$hash ) break;
			$list[ $hash['voice_id'] ] = $hash['played_count'];
		}
		return $list;
	}
	function getListByRecentRegistered( $limit=100 )
	{
		$sql = sprintf( "SELECT * FROM %s WHERE 1 ORDER BY `voice_id` DESC LIMIT 0, %d",
			self::TABLE_INFO, is_numeric($limit) ? $limit : 100 );
		$state = $this->pdo->query( $sql );

		$list = array();
		while( true )
		{
			$hash = $state->fetch( PDO::FETCH_ASSOC );
			if( !$hash ) break;
			$list[] = $hash['voice_id'];
		}
		return $list;
	}
	
	function delete( VoiceInfo $i )
	{
		$params = array( ':vid' => $i->voiceid );
		$tables = array(
			self::TABLE_INFO,
			self::TABLE_DETAIL,
			self::TABLE_PLAYING );
		foreach( $tables as $table )
		{
			$sql = sprintf( "DELETE FROM %s WHERE `voice_id`=:vid",
				$table );
			$state = $this->pdo->prepare( $sql );
			if( !$state->execute( $params ) ) return false;
		}
		return true;
	}
}