<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "web/LoginSession.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class DownloadVoice
{
	const ERROR_NO_SESSION = 'No session !';
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
		if( !LoginSession::get()->check() ) throw new VoiceException(self::ERROR_NO_SESSION);

		if( $_REQUEST['id'] ) $this->info = MediaInfo::getInfo( $_REQUEST['id'] );
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
			readfile($path);
		}
	}
}
$instance = new DownloadVoice();
$instance->run();
?>