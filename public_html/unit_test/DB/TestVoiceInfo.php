<?php
require_once( "../../../configure.php" );
require_once( INCLUDE_DIR . "util/UnitTestRun.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class TestVoiceInfo extends PHPUnit2_Framework_TestCase
{
	public $data;
	public $dataSub;
	
	function __construct()
	{
		$this->data = array(
			'voice_id' => 999,
			'user_id' => 999,
			'dst' => 'c:/temp/999.mp3',
			'upload_time' => '2010-04-01',
			'size_kb' => 999,
			'image_id' => 999,
			'title' => 'title',
			'artist' => 'artist',
			'description' => 'description',
			'tags' => 'hoge huga',
			'played_count' => 999,
		);
		$this->dataSub = array(
			'voice_id' => 999,
			'user_id' => 999,
			'dst' => 'c:/temp/998.mp3',
			'upload_time' => '2010-03-31',
			'size_kb' => 998,
			'image_id' => 998,
			'title' => 'title_',
			'artist' => 'artist_',
			'description' => 'description_',
			'tags' => 'hoge huga',
			'played_count' => 998,
		);
		
	}
	
	function testCopyInfo()
	{
		$info = new VoiceInfo( $this->data );

		$this->assertSame( 999, $info->voiceid );
		$this->assertSame( 999, $info->userid );
		$this->assertSame( 'c:/temp/999.mp3', $info->dst );
		$this->assertSame( '2010-04-01', $info->uploadTime->format('Y-m-d') );
		$this->assertSame( 999, $info->sizeKb );
		$this->assertSame( 999, $info->imageid );
		$this->assertSame( 'title', $info->title );
		$this->assertSame( 'artist', $info->artist );
		$this->assertSame( 'description', $info->description );
		$this->assertSame( array('hoge','huga'), $info->tags );
		$this->assertSame( 999, $info->playedCount );
		$this->assertSame( true, $info->isVisible );
	}
	
	function testCheckDetail()
	{
		$exception = 0;
		$longName = '';
		for( $i = 0; $i < 512; $i++ ) $longName .= 'a';
		
		$info = new VoiceInfo(array('title'=>$longName));
		try{	$info->checkDetail();	}catch(VoiceException $ex){	$exception++;	}
		
		$info = new VoiceInfo(array('artist'=>$longName));
		try{	$info->checkDetail();	}catch(VoiceException $ex){	$exception++;	}

		$info = new VoiceInfo(array('description'=>$longName));
		try{	$info->checkDetail();	}catch(VoiceException $ex){	$exception++;	}

		$info = new VoiceInfo(array('tags'=>$longName));
		try{	$info->checkDetail();	}catch(VoiceException $ex){	$exception++;	}
		
		$this->assertSame( 4, $exception );
	}
	
	function testToArray()
	{
		$info = new VoiceInfo( $this->data );
		$array = $info->toArray();
		foreach( $array as $key => $value )
		{
			$this->assertSame( $value, $this->data[ $key ] );
		}
	}
	
	function testUpdateInfo()
	{
		$info = new VoiceInfo( $this->data );
		$infoSub = new VoiceInfo( $this->dataSub );
		
		$store = new VoiceInfoDB();
		$store->updateInfo( $infoSub );	
		$result = $store->getInfo( 999 );
		
		$this->assertSame( 'c:/temp/998.mp3', $result->dst );
		
		$store->updateInfo( $info );
		$result = $store->getInfo( 999 );
		
		$this->assertSame( 'c:/temp/999.mp3', $result->dst );
		$this->assertSame( 999, $result->sizeKb );
	}
	
	function testUpdateDetail()
	{
		$info = new VoiceInfo( $this->data );
		$infoSub = new VoiceInfo( $this->dataSub );
		
		$store = new VoiceInfoDB();
		$store->updateDetail( $infoSub );
		$result = $store->getDetail( $infoSub );
		
		$this->assertSame( 'title_', $result->title );
		
		$store->updateDetail( $info );
		$result = $store->getDetail( $info );

		$this->assertSame( 999, $result->imageid );
		$this->assertSame( 'title', $result->title );
		$this->assertSame( 'artist', $result->artist );
		$this->assertSame( 'description', $result->description );
		$this->assertSame( array('hoge','huga'), $result->tags );
	}
	
	function testUpdatePlaying()
	{
		$info = new VoiceInfo( $this->data );
		$infoSub = new VoiceInfo( $this->dataSub );
		
		$store = new VoiceInfoDB();
		$store->updatePlaying( $infoSub );
		$result = $store->getPlaying( $infoSub );

		$this->assertSame( 998, $result->playedCount );

		$store->updatePlaying( $info );
		$result = $store->getPlaying( $info );

		$this->assertSame( 999, $result->playedCount );
	}
	
	function testGetInfosByUser()
	{
		$store = new VoiceInfoDB();
		$infos = $store->getInfosByUser( 999 );
		
		$this->assertSame( true, is_array($infos) );
	}
	
	function testGetListByRecent()
	{
		$store = new VoiceInfoDB();
		$list = $store->getListByRecent( 10 );
		
		$this->assertSame( true, is_array($list) );
	}
}
runUnitTest( 'TestVoiceInfo', INCLUDE_DIR . "DB/VoiceInfo.php" );
?>