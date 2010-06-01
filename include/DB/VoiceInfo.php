<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class VoiceInfo
{
	public $voiceid;
	public $userid;
	public $dst;
	public $playedCount;
	public $playble;
	public $uploadDate;
	
	public $thumbnailid;
	public $title;
	public $description;
	public $tags;
	
	function __construct( $p=null )
	{
		if( is_array($p) )
		{
			$this->voiceid = int($p['voice_id']);
			$this->userid = int($p['user_id']);
			$this->title = $p['title'];
			$this->dst = $p['dst'];
			$this->playedCount = int($p['played_count']);
			if(is_string($p['upload_date'])) $this->uploadDate = new DateTime($p['upload_date']);
			
			$this->thumbnailid = int($p['thumbnail_id']);
			$this->title = $p['title'];
			$this->description = $p['description'];
			if(is_string($p['tags'])) $this->tags = mb_split(' ',$p['tags']);
		}
	}
}

class VoiceInfoDB extends VoicesDB
{
	const TABLE_INFO = 'voice_info';
	const TABLE_DETAIL = 'voice_detail';
	
	function setInfo( VoiceInfo $info )
	{	///// info
		$now = date('c');
		$sql = sprintf("INSERT INTO %s (`user_id`,dst`,`upload_time`) VALUES(:userid,:dst,:now)",
			self::TABLE_INFO);
		$params = array(
			':userid' => $info->userid,
			':dst' => $info->dst,
			':now' => $now );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$sql = sprintf("SELECT * FROM %s WHERE `user_id`=:userid AND `upload_time`=:now LIMIT 1",
			self::TABLE_INFO);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		///// detail
		$hash = $state->fetch( PDO::FETCH_ASSOC );
		$vid = $hash['voice_id'];
		if( !$vid ) return null;
		$sql = sprintf("INSERT INTO %s (`voice_id`,`thumbnail_id`,`title`,`description`,`tags`) VALUES(:voiceid,:thumbid,:title,:dsc,:tags)",
			self::TABLE_DETAIL);
		$params = array(
			':voiceid' => $vid,
			':thumbid' => $info->thumbnailid,
			':title' => $info->title,
			':dsc' => $info->description,
			':tags' => is_array($info->tags) ? implode(' ',$info->tags) : '' );
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$info->voiceid = $vid;
		return $info;
	}
	
	function updateDetail( VoiceInfo $info )
	{
		
	}
	
	function getInfo( $vid, $needdetail=false )
	{
		$params = array(
			':voiceid' => $vid );
		$sql = sprintf("SELECT * FROM %s WHERE `voice_id`=:voiceid LIMIT 1",
			self::TABLE_INFO);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return null;
		
		$info = $state->fetch( PDO::FETCH_ASSOC );
		if( !$needdetail ) return new VoiceInfo( $info );
		
		$sql = sprintf("SELECT * FROM %s WHERE `voice_id`=:voiceid LIMIT 1",
			self::TABLE_DETAIL);
		$state = $this->pdo->prepare( $sql );
		if( !$state->execute( $params ) ) return new VoiceInfo( $info );
		
		$detail = $state->fetch( PDO::FETCH_ASSOC );
		return new VoiceInfo( array_merge($info,$detail) );
	}
}