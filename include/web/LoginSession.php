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
	
	const SESSION_KEY = 'session_key';
	const SESSION_USERID = 'session_userid';
	const SESSION_EXPIRE_DAY = 3;
	
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

		$_REQUEST[ self::SESSION_USERID ] = $this->tempKey->userid;
		$_REQUEST[ self::SESSION_KEY ] = $this->tempKey->tempKey;
		
		switch($this->mode)
		{
		case self::MODE_COOKIE:
			$this->updateCookie( $this->tempKey, $this->expireTime() );
			break;
		}
	}
	
	function clear()
	{
		switch($this->mode)
		{
		case self::MODE_COOKIE:
			$this->updateCookie( new TempKey(), 0 );
			break;
		}
	}
	
	protected function updateCookie( TempKey $key, $timeout )
	{
		setcookie( self::SESSION_USERID, $key->userid, $timeout, $this->getHostPath(), $this->getHost() );
		setcookie( self::SESSION_KEY, $key->tempKey, $timeout, $this->getHostPath(), $this->getHost() );
	}
	
	
	public function getTempKey()
	{
		$key = $_REQUEST[ self::SESSION_KEY ];
		$userid = $_REQUEST[ self::SESSION_USERID ];

		if( $key && $userid ) return new TempKey( array('user_id'=>$userid,'temp_key'=>$key) );
		
		return null;
	}
	
	function check()
	{
		$this->tempKey = $this->getTempKey();
		if( $this->tempKey && $this->tempKeyDB->authorizeTempKey( $this->tempKey ) )
		{
			$this->updateCookie( $this->tempKey, $this->expireTime() );
			return $this->tempKey->userid;
		}

		return null;
	}
	
	function getSessionArray()
	{
		$tempKey = $this->getTempKey();
		if( !$tempKey ) return null;
		
		return array(
			self::SESSION_USERID => $tempKey->userid,
			self::SESSION_KEY => $tempKey->tempKey,
			'session_urlparam' => sprintf("session_userid=%d&session_key=%s", $tempKey->userid, $tempKey->tempKey)
			);
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
	protected function expireTime()
	{
		return time() + self::SESSION_EXPIRE_DAY * 86400;
	}
}
?>