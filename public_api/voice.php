<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "web/LoginSession.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class DownloadVoice
{
	const ERROR_NO_SESSION = 'No session !';
	const ERROR_NO_VOICE_INFO = 'No voice info !';
	const ERROR_NO_FILE = 'No file !';
	
	protected $vDB;
	protected $vInfo;
	protected $session;
	
	function __construct( $options=null )
	{
		$this->vDB = $options['VoiceInfoDB'] ? $options['VoiceInfoDB'] : new VoiceInfoDB();
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
		
		if( $_REQUEST['id'] ) $this->vInfo = $this->vDB->getInfo( $_REQUEST['id'] );
		if( !$this->vInfo ) throw new VoiceException(self::ERROR_NO_VOICE_INFO);
	}
	function handle()
	{
		$path = $this->vInfo->dst;
		if( !file_exists($path) ) throw new VoiceException(self::ERROR_NO_FILE);
		
		header('Content-type: audio/mpeg');
		header('Content-Length: ' . filesize($path));
		readfile($path);
	}
}
$instance = new DownloadVoice();
$instance->run();
?>