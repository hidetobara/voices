<?php
require_once( "../../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/UserInfo.php" );


class ManageAccountWeb extends BaseWeb
{
	private $userDb;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'manage';
		$this->templateGroup = 'manage';
		$this->template = 'account.tpl';
		
		$this->userDb = $opt['UserDB'] ? $opt['UserDB'] : new UserDB();
	}
	
	function initialize()
	{
		$userid = (int)LoginSession::get()->check();
		if( !$userid || $userid >= MANAGER_ID_MAX )
		{
			$this->setRedirect( HOME_URL );
			return;
		}
		
		$this->command = $_REQUEST['command'];
	}

	function handle()
	{
		switch($this->command)
		{
			case 'new':
				$this->handleNew();
				break;
			case 'reset':
				$this->handleReset();
				break;
		}
	}
	
	private function handleNew()
	{
		$_POST['password'] = "0000";
		$_POST['user_status'] = "ACT";
		
		$userInfo = new UserInfo( $_POST );
		$tmpInfo = $this->userDb->getUser( $userInfo );
		if( $tmpInfo ) throw new VoiceException( "This username has already exists !" );

		$this->userDb->addUser( $userInfo );
		$userInfo = $this->userDb->getUser( $userInfo );
		
		$this->assign( 'user_info', $userInfo );
		$this->assign( 'message', 'Made a new account !' );
	}
	private function handleReset()
	{		
		$userInfo = new UserInfo( $_POST );
		$userInfo = $this->userDb->getUser( $userInfo );
		if( !$userInfo ) throw new VoiceException( "This user does not exist !" );
		
		$userInfo->copyInfo( array("password"=>"0000") );
		$this->userDb->updateUser( $userInfo );
		
		$this->assign( 'user_info', $userInfo );
		$this->assign( 'message', 'Reset this account !' );
	}
}

$web = new ManageAccountWeb();
$web->run();

?>