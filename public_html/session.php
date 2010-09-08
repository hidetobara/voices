<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/UserInfo.php" );

class SessionWeb extends BaseWeb
{
	protected $mode;
	const MODE_NOT_LOGINED = 1;
	const MODE_LOGINED = 2;
	
	protected $user;
	protected $db;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'session';
		$this->template = 'session.tpl';
		
		$this->db = $opt['UserDB'] ? $opt['UserDB'] : new UserDB();
	}
	
	function initialize()
	{
		$this->user = new UserInfo( $_REQUEST );
		
		$this->mode = self::MODE_NOT_LOGINED;
		if( LoginSession::get()->check() )
		{
			$this->mode = self::MODE_LOGINED;
			$this->assign('logined',true);
		}
	}
	function handle()
	{
		$command = $_REQUEST['command'];

		switch($this->mode)
		{
			case self::MODE_NOT_LOGINED:
				if( $command!='login' ) break;
				$this->user = $this->db->authorizeUser( $this->user );
				if( !$this->user->userId ) throw new VoiceException(CommonMessages::get()->msg('LOGIN_ERROR'));
				
				LoginSession::get()->make( $this->user->userId ); 
				
				$this->assignHash( LoginSession::get()->getSessionArray() );
				$this->assign('logined',true);
				break;
				
			case self::MODE_LOGINED:
				if( $command=='logout' )
				{
					LoginSession::get()->clear();
					$this->assign('logined',false);
				}
				break;
		}
	}
}
$web = new SessionWeb();
$web->run();
?>