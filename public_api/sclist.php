<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "web/BaseXml.php" );
require_once( INCLUDE_DIR . "DB/PlaylistInfo.php" );
require_once( INCLUDE_DIR . "DB/VoiceInfo.php" );


class PlaylistXml extends BaseXml
{
	protected $playlistDb;
	protected $voiceDb;
	protected $playlistInfo;
	
	protected $index;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->playlistDb = $opt['PlaylistInfoDB'] ? $opt['PlaylistInfoDB'] : new PlaylistInfoDB();
		$this->voiceDb = $opt['VoiceInfoDB'] ? $opt['VoiceInfoDB'] : new VoiceInfoDB();
	}
	
	protected function initialize()
	{
		$this->playlistInfo = $this->playlistDb->getInfo( $_REQUEST['id'] );
		if( !$this->playlistInfo ) throw new VoiceException(CommonMessages::get()->msg('NO_PLAYLIST'));
		
		$this->index = $_REQUEST['index'];
		if( !is_numeric($this->index) ) $this->index = 0; else $this->index++;
	}
	
	protected function handle()
	{
		///// current
		$vid = $this->playlistInfo->voiceids[ $this->index ];
		if( !$vid )
		{
			$this->index = 0;
			$vid = $this->playlistInfo->getVoiceId( 0 );
		}
		
		$vinfo = $this->voiceDb->getInfo( $vid );
		if( !$vinfo ) throw new VoiceException(CommonMessages::get()->msg('NO_VOICE_INFO'));
		$this->voiceDb->getDetail( $vinfo );
		
		$this->assign( 'status', 'ok' );
		$this->assign( 'current_voice', $vinfo->toArray() );
		
		///// previous
		if( $this->index > 0 )
		{
			$pid = $this->playlistInfo->getVoiceId( $this->index -1 );
			if( $pid ) $pinfo = $this->voiceDb->getInfo( $pid );
			if( $pinfo ) $this->assign( 'previous_voice', $pinfo->toArray() );
		}

		///// next
		$nid = $this->playlistInfo->getVoiceId( $this->index +1 );
		if( $nid ) $ninfo = $this->voiceDb->getInfo( $nid );
		if( $ninfo ) $this->assign( 'next_voice', $ninfo->toArray() );
		
		$stack = array(
			'mode' => 'playlist',
			'playlist_id' => $this->playlistInfo->playlistid,
			'index' => $this->index
			);
		$this->assign( 'stack', $stack );
	}
}

switch( $_REQUEST['mode'] )
{
	case 'playlist':
		$xml = new PlaylistXml();
		break;
}
if( $xml ) $xml->run();
?>