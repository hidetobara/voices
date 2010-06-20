<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );

class DownloadException extends Exception
{
}

class Download
{
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
		if( $_REQUEST['vid'] ) $this->vInfo = $this->vDB->getInfo( $_REQUEST['vid'] );
		if( !$this->vInfo ) throw new DownloadException(self::ERROR_NO_VOICE_INFO);
	}
	function handle()
	{
		$path = $this->vInfo->dst;
		if( !file_exists($path) ) throw new DownloadException(self::ERROR_NO_FILE);
		
		header('Content-type: audio/mpeg');
		header('Content-Length: ' . filesize($path));
		readfile($path);
	}
}
$instance = new Download();
$instance->run();
?>