<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "File/VoiceFile.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class UploadListWeb extends BaseWeb
{
	protected $voiceInfoDb;
	protected $voiceFile;
	
	protected $command;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'uploadlist';
		$this->template = 'uploadlist.tpl';
		
		$this->voiceInfoDb = $opt['VoiceInfoDB'] ? $opt['VoiceInfoDB'] : new VoiceInfoDB();
		$this->voiceFile = $opt['VoiceFile'] ? $opt['VoiceFile'] : new VoiceFile();
	}
	
	function initialize()
	{
		$this->checkSession();
		
		if( is_string($_REQUEST['command']) ) $this->command = $_REQUEST['command'];
	}
	function handle()
	{
		switch( $this->command )
		{
			case 'deleting':
				$voice = $this->voiceInfoDb->getInfo( $_REQUEST['voice_id'] );
				$this->voiceInfoDb->getDetail( $voice );
				$this->assign( 'mode', 'deleting' );
				$this->assign( 'target_voice_info', $voice );
				break;
			
			case 'delete':
				$voice = $this->voiceInfoDb->getInfo( $_REQUEST['voice_id'] );
				if( !$voice ) break;
				$this->voiceFile->delete( $voice );
				$this->voiceInfoDb->delete( $voice );
				break;
		}
		
		$infos = $this->voiceInfoDb->getInfosByUser( $this->userid );
		foreach( $infos as $info )
		{
			$this->voiceInfoDb->getDetail($info);
		}
		$this->assign( 'my_voice_infos', $infos );
		
		$amount = 0;
		foreach( $infos as $info ) $amount += $info->sizeKb;
		$this->assign( 'my_total_size', $amount );
		$this->assign( 'size_limit', PERSONAL_SIZE_LIMIT_KB );
	}
}
$web = new UploadListWeb();
$web->run();
?>