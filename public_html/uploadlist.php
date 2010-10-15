<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "File/VoiceFile.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );
require_once( INCLUDE_DIR . "File/ImageFile.php" );
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );
require_once( INCLUDE_DIR . "web/MediaPaging.php" );
require_once( INCLUDE_DIR . "web/ShortSession.php" );


class UploadListWeb extends BaseWeb
{
	protected $voiceDb;
	protected $voiceFile;
	protected $imageDb;
	protected $imageFile;
	
	protected $command;
	protected $page;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'uploadlist';
		$this->template = 'uploadlist.tpl';
		
		$this->voiceDb = $opt['voiceDb'] ? $opt['voiceDb'] : new voiceInfoDB();
		$this->voiceFile = $opt['VoiceFile'] ? $opt['VoiceFile'] : new VoiceFile();

		$this->imageDb = $opt['ImageInfoDB'] ? $opt['ImageInfoDB'] : new ImageInfoDB();
		$this->imageFile = $opt['ImageFile'] ? $opt['ImageFile'] : new ImageFile();
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();
		
		if( is_string($_REQUEST['command']) ) $this->command = $_REQUEST['command'];
		if( is_numeric($_REQUEST['page']) ) $this->page = $_REQUEST['page'];
	}
	function handle()
	{
		$voice = $this->voiceDb->getInfo( $_REQUEST['voice_id'] );
		switch( $this->command )
		{
			case 'deleting':
				if( !$voice ) break;
				ShortSession::get()->updateCookie();
				$this->voiceDb->getDetail( $voice );
				$this->assign( 'mode', 'deleting' );
				$this->assign( 'target_voice_info', $voice );
				break;
			case 'delete':
				if( !$voice ) break;
				ShortSession::get()->check();
				$this->voiceFile->delete( $voice );
				$this->voiceDb->delete( $voice );
				break;
				
			case 'editing':
				if( !$voice ) break;
				ShortSession::get()->updateCookie();
				$this->voiceDb->getDetail( $voice );
				$this->assign( 'mode', 'editing' );
				$this->assign( 'target_voice_info', $voice );
				break;
			case 'edit':
				ShortSession::get()->check();
				$this->handleEdit();
				break;
		}
		
		$infos = $this->voiceDb->getInfosByUser( $this->userid );
		$this->assignMySize( $infos );
		
		if( count($infos) > 0 )
		{
			$paging = new MediaPaging();
			$paging->generate( $infos, $this->page );
			$this->assign( 'paging', $paging );
		}
		
		$this->assignMyInfos( $paging->items );
	}
	
	function handleEdit()
	{
		$voiceNew = new VoiceInfo( $_REQUEST );
		$voiceNew->checkDetail();

		$imageFile = $_FILES['image_file'];
		if( $imageFile['size'] > 0 )
		{
			$imageInfo = $this->imageDb->newInfo( $this->userid );
			$this->imageFile->save( $imageFile, $imageInfo );
			$voiceNew->imageid = $imageInfo->imageid;
		}
		
		$this->voiceDb->updateDetail( $voiceNew );
	}
	
	function assignMyInfos( $infos )
	{
		if( count($infos) == 0 ) return;
		
		foreach( $infos as $info )
		{
			$this->voiceDb->getDetail($info);
		}
		$this->assign( 'my_voice_infos', $infos );
	}
	
	function assignMySize( $infos )
	{
		$amount = 0;
		foreach( $infos as $info ) $amount += $info->sizeKb;
		$this->assign( 'my_total_size', $amount );
		$this->assign( 'size_limit', PERSONAL_SIZE_LIMIT_KB );
	}
}
$web = new UploadListWeb();
$web->run();
?>