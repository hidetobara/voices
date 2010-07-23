<?php
require_once( INCLUDE_DIR . "DB/TempKey.php" );

class LoginSession
{
	static protected $Instance;
	static function get()
	{
		if( !LoginSession::$Instance ) LoginSession::$Instance = new LoginSession();
		return LoginSession::$Instance;
	}
	
	const SESSION_NAME = 'voice_session';
	const SESSION_USERID = 'voice_userid';
	
	protected $tempKeyDB;
	protected $tempKey;
	
	protected $mode;
	const MODE_COOKIE = 1;
	const MODE_FLAG = 2;
	
	function __construct( $options=null )
	{
		$this->tempKeyDB = $options['TempKeyDB'] ? $options['TempKeyDB'] : new TempKeyDB();

		$this->mode = self::MODE_FLAG;
		if( $this->isCookieEnable() ) $this->mode = self::MODE_COOKIE;
	}
	
	function make( $userid )
	{
		$this->tempKey = new TempKey( array('user_id'=>$userid) );
		$this->tempKeyDB->updateTempKey( $this->tempKey );

		switch($this->mode)
		{
		case self::MODE_COOKIE:
			$timeout = time() + 7 * 86400;
			$out = setcookie( self::SESSION_USERID, $this->tempKey->userId, $timeout, $this->getHostPath(), $this->getHost() );
			setcookie( self::SESSION_NAME, $this->tempKey->tempKey, $timeout, $this->getHostPath(), $this->getHost() );
			break;
		}
	}
	function clear()
	{
		switch($this->mode)
		{
		case self::MODE_COOKIE:
			setcookie( self::SESSION_NAME, '', 0, $this->getHostPath(), $this->getHost() );
			break;
		}
	}
	function check()
	{
		if( self::MODE_COOKIE )
		{
			$key = $_COOKIE[ self::SESSION_NAME ];
			$userid = $_COOKIE[ self::SESSION_USERID ];
		}

		if( !$key ) $key = $_REQUEST[ 'temp_key' ];
		if( !$userid ) $userid = $_REQUEST[ 'user_id' ];
		
		if( $key && $userid )
		{
			$this->tempKey = new TempKey( array('user_id'=>$userid,'temp_key'=>$key) );
			if( $this->tempKeyDB->authorizeTempKey( $this->tempKey ) ) return $userid;
		}		
		return null;
	}
	
	protected function isCookieEnable()
	{
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if( preg_match('@(Mozilla|Firefox|Safari|Netscape|Opera)@i', $ua )==1 ) return true;
		return false;
	}
	protected function getHost()
	{
		$cells = parse_url( HOME_URL );
		return $cells['host'];
	}
	protected function getHostPath()
	{
		$cells = parse_url( HOME_URL );
		return $cells['path'];
	}
}
?>