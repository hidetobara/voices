<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );

class VoicelistWeb extends BaseWeb
{
	protected $mode;
	
	protected $play;

	protected $voiceDb;
	protected $playDb;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'voicelist';
		$this->template = 'voicelist.tpl';
		
		$this->playDb = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
		$this->voiceDb = $opt['VoiceInfoDB'] ? $opt['VoiceInfoDB'] : new VoiceInfoDB();
	}
	
	function initialize()
	{
		$this->checkSession();
		
		$this->play = new PlaylistInfo( $_REQUEST );
		if( $this->play->playlistid )
		{
			$this->play = $this->playDb->getInfo( $this->play->playlistid );
		}
		
		$this->assign( 'playlist_info', $this->play );
	}
	
	function handle()
	{		
		switch( $_REQUEST[ 'command' ] )
		{
			case 'select':
				$this->handleSelect();
			
			case 'delete':
			case 'add':
			default:
				$this->handleAll( $_REQUEST[ 'command' ] );
				break;
		}
		$this->assign('mode', $this->mode);
	}
	
	function handleAll( $command )
	{
		if( !$this->play ) throw new VoiceException(CommonMessages::get()->msg('NO_PLAYLIST'));
		
		$voices = array();
		if( count($this->play->voiceids) > 0 )
		{
			foreach( $this->play->voiceids as $vid )
			{
				$voice = $this->voiceDb->getInfo( $vid );
				$this->voiceDb->getDetail( $voice );
				$voices[ $vid ] = $voice;
			}
		}
		
		if( $command == 'add' )
		{
			$vid = intval( $_REQUEST['voice_id'] );
			if( !$vid ) throw new VoiceException(CommonMessages::get()->msg('NO_VOICE_INFO'));

			$voice = $this->voiceDb->getInfo( $vid );
			$this->voiceDb->getDetail( $voice );
			$voices[ $vid ] = $voice;
			$this->play->voiceids = array_keys( $voices );
			$this->playDb->updateInfo( $this->play );
		}
		
		if( $command == 'delete' )
		{
			$vid = intval( $_REQUEST['voice_id'] );
			if( !$vid ) throw new VoiceException(CommonMessages::get()->msg('NO_VOICE_INFO'));
			
			unset( $voices[ $vid ] );
			$this->play->voiceids = array_keys( $voices );
			$this->playDb->updateInfo( $this->play );
		}
		
		$this->assign( 'voice_array', $voices );
	}
	
	function handleSelect()
	{
		$vid = $_REQUEST[ 'voice_id' ];
		if( !$vid ) throw new VoiceException(CommonMessages::get()->msg('NO_VOICE_INFO'));
		$voiceInfo = $this->voiceDb->getInfo( $vid );
		if( !$voiceInfo ) throw new VoiceException(CommonMessages::get()->msg('NO_VOICE_INFO'));
		$this->voiceDb->getDetail( $voiceInfo );
		
		$array = $this->playDb->getUserInfos( $this->userid );
		if( count($array)==0 ) throw new VoiceException(CommonMessages::get()->msg('NO_PLAYLIST'));
		
		$this->mode = 'select';
		$this->assign( "voice_info", $voiceInfo );
		$this->assign( "playlist_array", $array );
	}
}
$web = new VoicelistWeb();
$web->run();
?>