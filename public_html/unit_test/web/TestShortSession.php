<?php
require_once( "../../../configure.php" );
require_once( INCLUDE_DIR . "util/UnitTestRun.php" );
require_once( INCLUDE_DIR . "web/ShortSession.php" );


class TestShortSession extends PHPUnit2_Framework_TestCase
{
	public $userid = 999;
	
	function __construct()
	{
		$_REQUEST[ LoginSession::SESSION_USERID ] = $this->userid;
	}
	
	function testMakeCheck()
	{
		$date = new DateTime( sprintf("-%d min", ShortSession::ALIVE_TIME_MIN -1) );
		$key = ShortSession::get()->make( $date );
		$_REQUEST[ ShortSession::SESSION_SHORT ] = $key;
		$result = ShortSession::get()->check();
		
		$this->assertSame( $this->userid, $result );
	}

	function testMakeCheckFail()
	{
		$date = new DateTime( sprintf("-%d min", ShortSession::ALIVE_TIME_MIN +1) );
		$key = ShortSession::get()->make( $date );
		$_REQUEST[ ShortSession::SESSION_SHORT ] = $key;
		try
		{
			$result = ShortSession::get()->check();
		}
		catch(VoiceException $ex)
		{
			return;
		}	
		$this->fail();
	}
}
runUnitTest( 'TestShortSession', INCLUDE_DIR . "web/ShortSession.php" );
?>