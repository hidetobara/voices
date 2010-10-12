<?php
require_once( "../../../configure.php" );
require_once( INCLUDE_DIR . "util/UnitTestRun.php" );
require_once( INCLUDE_DIR . "DB/UserInfo.php" );


class TestUserInfo extends PHPUnit2_Framework_TestCase
{
	public $data;
	
	function __construct()
	{
		$this->data = array(
			'user_id' => 999,
			'username' => 'test',
			'login_time' => '2010-04-01',
			'user_status' => 'ACT',
			'password' => '0000',
			);
	}
	
	function testCopyInfo()
	{
		$info = new UserInfo( $this->data );
		
		$this->assertEquals(999, $info->userid);
		$this->assertEquals('test', $info->username);
		$this->assertEquals('2010-04-01', $info->loginTime);
		$this->assertEquals('ACT', $info->userStatus);		
	}
	
	function testCheckUsername()
	{
		$info = new UserInfo( $this->data );
		$this->assertEquals("", $info->checkUsername());
		
		$info = new UserInfo( array('username'=>'!test') );
		$this->assertEquals(UserInfo::CODE_USERNAME_USABLE, $info->checkUsername());
		$info = new UserInfo( array('username'=>'abc') );
		$this->assertEquals(UserInfo::CODE_USERNAME_LENGTH, $info->checkUsername());
	}
	
	function testCheckPassword()
	{
		$info = new UserInfo( $this->data );
		$this->assertEquals("", $info->checkPassword());
		
		$info = new UserInfo( array('password'=>'ab') );
		$this->assertEquals(UserInfo::CODE_PASSWORD_LENGTH, $info->checkPassword());
	}
	
	function testUpdateUser()
	{
		$info = new UserInfo( $this->data );
		$store = new UserDB();
		$store->updateUser( $info );
		$result = $store->getUser( $info );
		
		$this->assertEquals( $info->userid, $result->userid );
		$this->assertEquals( $info->username, $result->username );
		$this->assertEquals( $info->userStatus, $result->userStatus );
	}
	
	function testAuthorizeUser()
	{
		$info = new UserInfo( $this->data );
		$store = new UserDB();
		$store->updateUser( $info );
		$result = $store->authorizeUser( $info );

		$this->assertEquals( $info->userid, $result->userid );
		$this->assertEquals( $info->username, $result->username );
		
		$fake = new UserInfo( array('username'=>'test','password'=>1111) );
		$result = $store->authorizeUser( $fake );
		$this->assertEquals( null, $result );
	}
}
runUnitTest( 'TestUserInfo', INCLUDE_DIR . "DB/UserInfo.php" );
?>