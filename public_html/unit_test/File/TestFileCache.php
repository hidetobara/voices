<?php
require_once( "../../../configure.php" );
require_once( INCLUDE_DIR . "util/UnitTestRun.php" );
require_once( INCLUDE_DIR . "File/FileCache.php" );


class TestFileCache extends PHPUnit2_Framework_TestCase
{
	public $data;
	public $name = 'test';
	
	function __construct()
	{
		$this->data = array('hoge'=>'huga');
	}
	
	function testSet()
	{
		$cache = new FileCache();
		$cache->set( $this->name, $this->data, new DateTime('+1 day') );
		$result = $cache->get( $this->name );
		
		$this->assertSame( $this->data, $result );
	}
	
	function testSet_expire()
	{
		$cache = new FileCache();
		$cache->set( $this->name, $this->data, new DateTime('-1 day') );
		$result = $cache->get( $this->name );
		
		$this->assertSame( null, $result );
	}
}
runUnitTest( 'TestFileCache', INCLUDE_DIR . "File/FileCache.php" );
?>