<?php
require_once( "../../../configure.php" );
require_once( INCLUDE_DIR . "util/UnitTestRun.php" );
require_once( INCLUDE_DIR . "DB/TempKey.php" );


class TestTempKey extends PHPUnit2_Framework_TestCase
{
	public $data;
	
	function __construct()
	{
		$date = new DateTime();
		$this->data = array(
			'user_id' => 999,
			'update_date' => $date->format('Y-m-d') );
	}
	
	function testCopyRecord()
	{
		$key = new TempKey( $this->data );
		
		$this->assertSame( $this->data['user_id'], $key->userid );
		$this->assertSame( $this->data['update_date'], $key->updateDate );
	}
	
	function testIsAlive()
	{
		$date = new DateTime("-3 day");
		$key = new TempKey( array('update_date'=>$date->format("y-m-d")) );
		
		$this->assertSame( false, $key->isAlive(2) );
		$this->assertSame( true, $key->isAlive(4) );
	}
	
	function testAuthorizeTempKey()
	{
		$key = new TempKey( $this->data );
		$store = new TempKeyDB();
		$store->updateTempKey( $key );
		$result = $store->authorizeTempKey( $key );
		
		$this->assertSame( $key->userid, $result->userid );
		$this->assertSame( $key->tempKey, $result->tempKey );
		
		$dateOrigin = new DateTime( $key->updateDate );
		$dateResult = new DateTime( $result->updateDate );
		$this->assertSame( $dateOrigin->format('Y-m-d'), $dateResult->format('Y-m-d') );
	}
}
runUnitTest( 'TestTempKey', INCLUDE_DIR . "DB/TempKey.php" );
