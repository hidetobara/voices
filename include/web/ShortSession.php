<?php
require_once( INCLUDE_DIR . "web/LoginSession.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );


class ShortSession
{
	static protected $Instance;
	static function get( $opt=null )
	{
		if( !self::$Instance ) self::$Instance = new self( $opt );
		return self::$Instance;
	}

	const ERROR_NO_USERID = 'No userid !';
	const ERROR_NO_SHORT_SESSION = 'No short session !';
	const ERROR_INVALID_SESSION = 'Out-of-date short session !';

	const ALIVE_TIME_MIN = 10;
	const SESSION_SHORT = 'session_short';
	const LENGTH = 8;
	
	protected $userid;
	
	function __construct( $opt=null )
	{
		$this->userid = (int)$_REQUEST[ LoginSession::SESSION_USERID ];
		
		if( $opt['userid'] ) $this->userid = (int)$opt['userid'];
	}
	
	function make( $date=null )
	{
		if( !$this->userid ) throw new VoiceException( self::ERROR_NO_USERID );
		
		if( !$date ) $date = new DateTime();
		$timeprint = $date->format("Y-m-d H:i:00");

		$md5 = md5( $this->userid . PASSWORD_SEED . $timeprint );
		return substr( $md5, 0, self::LENGTH );
	}
	function updateCookie()
	{
		setcookie( self::SESSION_SHORT, $this->make(), time() + 60 * self::ALIVE_TIME_MIN, $this->getHostPath(), $this->getHost() );
	}
	function getSessionArray()
	{
		$made = $this->make();
		return array(
			self::SESSION_SHORT => $made,
			'short_session_urlparam' => sprintf( "%s=%s", self::SESSION_SHORT, $made )
			);
	}
	
	function check()
	{
		if( !$this->userid ) throw new VoiceException( self::ERROR_NO_USERID );
		
		$received = $_REQUEST[ self::SESSION_SHORT ];
		if( !$received ) throw new VoiceException( self::ERROR_NO_SHORT_SESSION );
		
		$date = new DateTime();
		for( $min = self::ALIVE_TIME_MIN; $min >= 0; $min-- )
		{
			$made = $this->make( $date );
			if( $received == $made ) return $this->userid;
			
			$date->modify("-1 min");
		}
		throw new VoiceException( self::ERROR_INVALID_SESSION );
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