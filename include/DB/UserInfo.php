<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );
loadLocalConf( 'secrect.conf' );


class UserInfo
{
	const CODE_USERNAME_USABLE = "Username includes invalid characters !";
	const CODE_USERNAME_LENGTH = "Username is too long or short !";
	const CODE_PASSWORD_LENGTH = "Password is too long or short !";

	public $userid;
	public $username;
	public $passwordMd5;
	public $passwordLength;
	public $loginTime;
	public $userStatus;
	
	function __construct( $obj=null )
	{
		if( $obj ) $this->copyInfo( $obj );
	}
	
	function copyInfo( $obj )
	{
		if( is_array($obj) )
		{
			if($obj['user_id']) $this->userid = intval($obj['user_id']);
			if($obj['username']) $this->username = $obj['username'];
			if($obj['login_time']) $this->loginTime = $obj['login_time'];
			if($obj['user_status']) $this->userStatus = $obj['user_status'];
			
			if($obj['password'])
			{
				$this->passwordLength = mb_strlen( $obj['password'] );
				$this->passwordMd5 = md5( $obj['password'] . PASSWORD_SEED );
			}
		}
	}
	
	function checkUsername()
	{
		$length = mb_strlen( $this->username );
		if( $length < 4 || 32 < $length ) return self::CODE_USERNAME_LENGTH;
		if( preg_match('/^[a-zA-Z0-9_\-]*$/', $this->username ) == 0 ) return self::CODE_USERNAME_USABLE;
		return "";
	}
	function checkPassword()
	{
		if( $this->passwordLength < 4 || 32 < $this->passwordLength ) return self::CODE_PASSWORD_LENGTH;
		return "";
	}
//	function checkMail()
//	{
//		if( preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $this->mail ) == 0 ) return self::CODE_MAIL_INVALID;
//		return "";
//	}
}

class UserDB extends BaseDB
{
	const TABLE_NAME = 'voices_users';
	
	function addUser( UserInfo $rec )
	{
		$sql = sprintf("INSERT INTO %s (username,password_md5,user_status) VALUES(:username,:passMd5,:userStatus )",
			self::TABLE_NAME);
		$state = $this->pdo->prepare( $sql );
		$state->execute(
			array( ':username'=>$rec->username, ':passMd5'=>$rec->passwordMd5, ':userStatus'=>$rec->userStatus )
			);
		return $state;
	}

	function updateUser( UserInfo $rec )
	{
		if( !$rec->userid ) return false;
		
		$sql = sprintf( "Update `%s` SET", self::TABLE_NAME );
		$params = array();
		if( $rec->username ){
			if( count($params) > 0 ) $sql .= ",";
			$sql .= " username = :username";
			$params[':username'] = $rec->username;
		}
		if( $rec->passwordMd5 ){
			if( count($params) > 0 ) $sql .= ",";
			$sql .= " password_md5 = :passMd5";
			$params[':passMd5'] = $rec->passwordMd5;
		}
		if( $rec->userStatus ){
			if( count($params) > 0 ) $sql .= ",";
			$sql .= " user_status = :userStatus";
			$params[':userStatus'] = $rec->userStatus;
		}
		$sql .= " WHERE user_id = :userid";
		$params[':userid'] = $rec->userid;

		$state = $this->pdo->prepare( $sql );
		return $state->execute( $params );
	}
	
	function authorizeUser( UserInfo $rec )
	{
		if( $rec->userid )
		{
			$sql = sprintf( "SELECT * FROM `%s` WHERE user_id = :userid",
				self::TABLE_NAME );
			$state = $this->pdo->prepare( $sql );
			$params = array( ':userid' => $rec->userid );
		}
		else if( $rec->username )
		{
			$sql = sprintf( "SELECT * FROM `%s` WHERE username LIKE :username",
				self::TABLE_NAME );
			$state = $this->pdo->prepare( $sql );
			$params = array( ':username' => $rec->username );
		}
		else
		{
			return null;
		}
		$state->execute( $params );
		$hash = $state->fetch(PDO::FETCH_ASSOC);
		if( !$hash ) return null;
		if( $hash['password_md5'] != $rec->passwordMd5 ) return null;
		
		$rec = new UserInfo( $hash );
		$sql = sprintf( "UPDATE `%s` SET login_time = NOW() WHERE user_id = :userid",
			self::TABLE_NAME );
		$state = $this->pdo->prepare( $sql );
		$state->execute(
			array( ':userid' => $rec->userid )
			);

		return $rec;
	}

	function getUser( UserInfo $rec )
	{
		$sql = sprintf( "SELECT * FROM `%s` WHERE",
			self::TABLE_NAME );
		if( $rec->userid )
		{
			$sql .= " user_id = :userid";
			$params = array( ':userid' => $rec->userid );
		}
		elseif( $rec->username )
		{
			$sql .= " username LIKE :username";
			$params = array( ':username' => $rec->username );
		}
		else
		{
			return null;
		}
		$state = $this->pdo->prepare( $sql );
		$state->execute( $params );
		$hash = $state->fetch(PDO::FETCH_ASSOC);
		if( !$hash ) return null;
		
		return new UserInfo($hash);
	}	
}
?>