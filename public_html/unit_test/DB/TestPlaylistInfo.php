<?php
require_once( "../../../configure.php" );
require_once( INCLUDE_DIR . "util/UnitTestRun.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );


class TestPlaylistInfo extends PHPUnit2_Framework_TestCase
{
	public $data;
	
	function __construct()
	{
		$this->data = array(
			'playlist_id' => 999,
			'user_id' => 999,
			'title' => 'test',
			'media_ids' => 'v1;v2;v3',
			'image_id' => 999,
			);
	}
	
	function testCopyInfo()
	{
		$info = new PlaylistInfo( $this->data );
		
		$this->assertSame( 999, $info->playlistid );
		$this->assertSame( 999, $info->userid );
		$this->assertSame( 'test', $info->title );
		$this->assertSame( 3, count($info->mediaids) );
		$this->assertSame( 999, $info->imageid );
	}
	
	function testGetMedia()
	{
		$info = new PlaylistInfo( $this->data );
		$info->addMediaId( 'v4' );
		
		$this->assertSame( 'v2', $info->getMediaId(1) );
		$this->assertSame( 'v4', $info->getMediaId(3) );
	}
	
	function testGetUpdateInfo()
	{
		$store = new PlaylistInfoDB();
		$info = new PlaylistInfo( $this->data );
		$store->updateInfo( $info );
		$result = $store->getInfo( 999 );
		
		$this->assertSame( $info->title, $result->title );
		$this->assertSame( $info->mediaids, $result->mediaids );
		
		$info->addMediaId( 'v4' );
		$info->title = 'hoge';
		$store->updateInfo( $info );
		$result = $store->getInfo( 999 );
		
		$this->assertSame( $info->title, $result->title );
		$this->assertSame( $info->mediaids, $result->mediaids );
	}
	
	function testGetUserInfos()
	{
		$store = new PlaylistInfoDB();
		$infos = $store->getUserInfos( 999 );
		$this->assertSame( true, count($infos)>0 );
	}
}
runUnitTest( 'TestPlaylistInfo', INCLUDE_DIR . "DB/PlaylistInfo.php" );
?>