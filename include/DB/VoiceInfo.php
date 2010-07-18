<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class VoiceInfo
{
	const TITLE_LENGTH_MAX = 64;
	const ARTIST_LENGTH_MAX = 64;
	const DESCRIPTION_LENGTH_MAX = 512;
	const TAG_LENGTH_MAX = 16;
	const TAGS_MAX = 10;
	
	public $voiceid;
	public $userid;
	public $dst;
	public $playable;
	public $uploadTime;
	
	public $imageid;
	public $title;
	public $artist;
	public $playedCount;
	public $description;
	public $tags;
	
	function __construct( $p=null )
	{
		if( is_array($p) )
		{
			$this->copyInfo( $p );
			$this->copyDetail( $p );
		}
	}
	function copyInfo( Array $p )
	{
		if(is_numeric($p['voice_id'])) $this->voiceid = intval($p['voice_id']);
		if(is_numeric($p['user_id'])) $this->userid = intval($p['user_id']);
		if($p['dst']) $this->dst = $p['dst'];
		if(is_string($p['upload_time'])) $this->uploadTime = new DateTime($p['upload_time']);
	}
	function copyDetail( Array $p )
	{
		if(is_numeric($p['image_id'])) $this->imageid = intval($p['image_id']);
		if($p['title']) $this->title = htmlspecialchars($p['title']);
		if($p['artist']) $this->artist = htmlspecialchars($p['artist']);
		if(is_numeric($p['played_count'])) $this->playedCount = intval($p['played_count']);
		if($p['description']) $this->description = htmlspecialchars($p['description']);
		if(is_string($p['tags']))
		{
			$this->tags = mb_split(' ',strip_tags($p['tags']));
			array_slice($this->tags, 0, self::TAGS_MAX);
		}
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
}

class VoiceInfoDB extends BaseDB
{
	const TABLE_INFO = 'voice_info';
	const TABLE_DETAIL = 'voice_detail';
	
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
		$sql = sprintf("UPDATE %s SET `dst`=:dst WHERE `voice_id`=:vid",
			self::TABLE_INFO );
		$params = array(
			':dst' => $info->dst,
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
	
	function getInfo( $vid )
	{
		$params = array(
			':vid' => $vid );
		$sql = sprintf("SELECT * FROM %s WHERE `voice_id`=:vid LIMIT 1",
			self::TABLE_INFO);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$info = $state->fetch( PDO::FETCH_ASSOC );
		return new VoiceInfo( $info );		
	}
	function getDetail( VoiceInfo $i )
	{
		if( !$i->voiceid ) return null;
		
		$params = array(
			':vid' => $i->voiceid );
		$sql = sprintf("SELECT * FROM %s WHERE `voice_id`=:vid LIMIT 1",
			self::TABLE_DETAIL);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$detail = $state->fetch( PDO::FETCH_ASSOC );
		$i->copyDetail( $detail );
		return $i;
	}
}