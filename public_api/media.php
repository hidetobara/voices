<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "web/LoginSession.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );

/*
 * format media/[userid]/[key]/[mediaid]
 */
class DownloadVoice
{
	const ERROR_NO_SESSION = 'No session !';
	const ERROR_INVALID_FORMAT = 'Invalid format !';
	const ERROR_NO_INFO = 'No info !';
	const ERROR_NO_FILE = 'No file !';
	
	protected $info;
	protected $session;
	
	function __construct( $options=null )
	{
	}
	
	function run()
	{
		try
		{
			$this->initialize();
			$this->handle();
		}
		catch(Exception $ex)
		{	
			header( "Content-Type: text/html; charset=UTF-8" );
			print $ex->getMessage();
		}
	}
	
	function initialize()
	{
		$path = $_SERVER['PHP_SELF'];
		$cells = mb_split( '/', $path );
		$filename = array_pop( $cells );
		$key = array_pop( $cells );
		$userid = array_pop( $cells );
		$file = mb_split( '\.', $filename );
		$mid = $file[0];
		$ext = $file[1];
		if( $ext != "mp3" ) throw new VoiceException(self::ERROR_INVALID_FORMAT);
		
		$_REQUEST[ LoginSession::SESSION_USERID ] = $userid;
		$_REQUEST[ LoginSession::SESSION_KEY ] = $key;
		if( !LoginSession::get()->check() ) throw new VoiceException(self::ERROR_NO_SESSION);

		$this->info = MediaInfo::getInfo( $mid );
		if( !$this->info ) throw new VoiceException(self::ERROR_NO_INFO);
	}
	function handle()
	{
		if( is_a($this->info,'VoiceInfo') )
		{
			$path = $this->info->dst;
			if( !file_exists($path) ) throw new VoiceException(self::ERROR_NO_FILE);

			$voiceDb = new VoiceInfoDB();
			$voiceDb->getPlaying($this->info);
			$this->info->playedCount++;
			$voiceDb->updatePlaying($this->info);
			
			header('Content-type: audio/mpeg');
			header('Content-Length: ' . filesize($path));
			header('Content-Disposition: filename=' . $this->info->mediaid. '.mp3');
			header('X-Pad: avoid browser bug');
			Header('Cache-Control: no-cache');
			readfile($path);
		}
	}
}
$instance = new DownloadVoice();
$instance->run();
?>