<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "File/VoiceFile.php" );
require_once( INCLUDE_DIR . "File/ImageFile.php" );


class UploadWeb extends BaseWeb
{
	public $command;

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
		$this->command = $_REQUEST['command'] ? $_REQUEST['command'] : 'input';
	}
	
	function handle()
	{
		$this->assign( 'mode', 'input' );
		
		switch( $this->command )
		{
			case 'upload':
				$this->uploadVoice( $_FILES['voice_file'], $_FILES['image_file'] );
				break;
		}
	}
	
	function uploadVoice( $vfile, $ifile )
	{
		$vinfo = new VoiceInfo( $_REQUEST );
		$this->assign( 'upinfo', $vinfo );
		$vinfo->checkDetail();
		
		if( $vfile['error'] ) throw new VoiceException(CommonMessages::get()->msg('NOT_UPLOAD'));
		if( $vfile['size'] > VOICE_SIZE_MAX_MB * 1024 * 1024 ) throw new VoiceException(CommonMessages::get()->msg('VOICE_SIZE_MAX_MB'));

		$vinfo = $this->voiceDb->newInfo( $this->userid );
		$vinfo->copyDetail( $_REQUEST );
		$dst = $this->voiceFile->save( $vfile, $vinfo );

		if( $ifile['size'] > 0 )
		{
			$iinfo = $this->imageDb->newInfo( $this->userid );
			$this->imageFile->save( $ifile, $iinfo );
			$vinfo->imageid = $iinfo->imageid;
		}

		$vinfo->dst = $dst;
		$this->voiceDb->updateInfo( $vinfo );
		$this->voiceDb->updateDetail( $vinfo );
		
		$this->assign( 'mode', 'uploaded' );
	}
}
$web = new UploadWeb();
$web->run();