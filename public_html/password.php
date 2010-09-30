<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "web/BaseWeb.php" );
require_once( INCLUDE_DIR . "DB/UserInfo.php" );


class MyPassword extends BaseWeb
{
	protected $userDb;
	
	protected $command;
	
	function __construct( $opt=null )
	{
		parent::__construct( $opt );
		
		$this->name = 'password';
		$this->template = 'password.tpl';
		
		$this->userDb = $opt['UserDB'] ? $opt['UserDB'] : new UserDB();
	}
	
	function initialize()
	{
		$this->checkSession();
		$this->assignSession();
		
		$this->command = $_REQUEST['command'];
	}
	
	function handle()
	{
		try
		{
			switch($this->command)
			{
				case 'input':
					$this->assign( 'mode', 'input' );
					break;
				case 'update':
					$this->handleUpdate();
					break;
					
				default:
					$this->setRedirect( SECURE_HOME_URL . "password.php?command=input" );
			}
		}
		catch(VoiceException $ex)
		{
			throw $ex;
		}
		catch(VoiceWarning $warn)
		{
			$this->assign( 'error', $warn->getMessage() );
			$this->assign( 'mode', 'input' );
		}
	}
	
	function handleUpdate()
	{
		$user = new UserInfo(
			array('user_id'=>$this->userid,'password'=>$_REQUEST['password']) );
		if( !$this->userDb->authorizeUser( $user ) ) throw new VoiceWarning( CommonMessages::get()->msg('AUTH_ERROR') );
		
		$passNew = $_REQUEST['password_new'];
		$passRetype = $_REQUEST['password_retype'];
		if( $passNew != $passRetype ) throw new VoiceWarning( CommonMessages::get()->msg('NOT_MATCH_PASSWORDS') );

		$user = new UserInfo(
			array('user_id'=>$this->userid,'password'=>$passNew) );
		$warn = $user->checkPassword();
		if( $warn ) throw new VoiceWarning( $warn );

		$this->userDb->updateUser( $user );
		
		$this->assign( 'mode', 'updated' );
	}
}
$page = new MyPassword();
$page->run();
?>