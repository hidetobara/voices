<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "web/SessionInfo.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );
require_once( INCLUDE_DIR . "File/VoiceFile.php" );

class UploadWeb extends BaseWeb
{
	const WARN_NOT_AUDIO = 'This file is not audio file.';
	
	public $mode;
	public $file;
	public $db;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->module = 'web';
		$this->name = 'upload';
		$this->template = 'voice.tpl';
		
		$this->file = $opt['VoiceFile'] ? $opt['VoiceFile'] : new VoiceFile();
		$this->db = $opt['VoiceInfoDB'] ? $opt['VoiceInfoDB'] : new VoiceInfoDB();
	}
	
	function initialize()
	{
		if( !SessionInfo::get()->check() ) throw new VoiceException(self::ERROR_NO_SESSION);
		
		//var_dump($_FILES);
		$this->mode = $_REQUEST['mode'];
		if( !$this->mode ) $this->mode = 'input';
		$this->assign('mode',$this->mode);
	}
	
	function handle()
	{
		switch( $this->mode )
		{
			case 'input':
				$this->assign('next_mode','upload');
				break;
			case 'upload':
				$this->uploadVoice( $_FILES['voice_file'] );
				break;
		}
	}
	
	function uploadVoice( $data )
	{
		$info = new VoiceInfo( $_REQUEST );
		$this->assign('upinfo',$info);
		try
		{
			if( $data['type'] != 'audio/mpeg' ) throw new VoiceException( self::WARN_NOT_AUDIO );
			$info->checkDetail();
		}
		catch(Exception $ex)
		{
			$this->assign('mode','input');
			$this->assign('next_mode','upload');
			throw $ex;
		}

		$info = $this->db->newInfo( $this->userid );
		$info->copyDetail( $_REQUEST );
		
		$dst = $this->file->save( $data, $info );
		$info->dst = $dst;
		$this->db->updateInfo( $info );
		$this->db->updateDetail( $info );
	}
}
$web = new UploadWeb();
$web->run();