<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "File/VoiceFile.php" );
require_once( INCLUDE_DIR . "File/ImageFile.php" );


class UploadWeb extends BaseWeb
{	
	public $mode;

	public $voiceFile;
	public $voiceDb;
	public $imageFile;
	public $imageDb;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'upload';
		$this->template = 'upload.tpl';
		
		$this->voiceFile = $opt['VoiceFile'] ? $opt['VoiceFile'] : new VoiceFile();
		$this->voiceDb = $opt['VoiceInfoDB'] ? $opt['VoiceInfoDB'] : new VoiceInfoDB();
		$this->imageFile = $opt['ImageFile'] ? $opt['ImageFile'] : new ImageFile();
		$this->imageDb = $opt['ImageInfoDB'] ? $opt['ImageInfoDB'] : new ImageInfoDB();
	}
	
	function initialize()
	{
		$this->checkSession();
		
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
				$this->uploadVoice( $_FILES['voice_file'], $_FILES['image_file'] );
				break;
		}
	}
	
	function uploadVoice( $vfile, $ifile )
	{
		$vinfo = new VoiceInfo( $_REQUEST );
		$this->assign( 'upinfo', $vinfo );
		try
		{
			$vinfo->checkDetail();
		}
		catch(Exception $ex)
		{
			$this->assign('mode','input');
			$this->assign('next_mode','upload');
			throw $ex;
		}

		if( $ifile )
		{
			$iinfo = $this->imageDb->newInfo( $this->userid );
			$this->imageFile->save( $ifile, $iinfo );
		}
		
		$vinfo = $this->voiceDb->newInfo( $this->userid );
		$vinfo->copyDetail( $_REQUEST );
		
		$dst = $this->voiceFile->save( $vfile, $vinfo );
		$vinfo->dst = $dst;
		if( $iinfo ) $vinfo->imageid = $iinfo->imageid;
		$this->voiceDb->updateInfo( $vinfo );
		$this->voiceDb->updateDetail( $vinfo );
	}
}
$web = new UploadWeb();
$web->run();