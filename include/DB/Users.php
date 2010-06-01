<?php
require_once( INCLUDE_DIR . "DB/VoicesDB.php" );

class UserRecord
{
	const CODE_USERNAME_USABLE = "使用できない文字が含まれています。";
	const CODE_USERNAME_LENGTH = "文字の長さが不適切です。";
	const CODE_PASSWORD_LENGTH = "パスワードの長さが不適当です。";
	const CODE_MAIL_INVALID = "invalid mail address !";

	public $userId;
	public $username;
	public $passwordMd5;
	public $mail;
	//public $nickname;
	public $registerDate;
	public $loginDate;
	public $userStatus;
	
	function __construct( $obj=null )
	{
		if( $obj ) $this->copyRecord( $obj );
	}
	
	function copyRecord( $obj )
	{
		if( is_array($obj) )
		{
			$this->userId = $obj['user_id'];
			$this->username = $obj['username'];
			if( $obj['password'] ) $this->passwordMd5 = md5( $obj['password'] );
			$this->mail = $obj['mail'];
			//$this->nickname = $obj['nickname'];
			$this->loginDate = $obj['login_date'];
			$this->registerDate = $obj['register_date'];
			$this->userStatus = $obj['user_status'];
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
		if( !$this->passwordMd5 ) return self::CODE_PASSWORD_LENGTH;
		return "";
	}
	function checkMail()
	{
		if( preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $this->mail ) == 0 ) return self::CODE_MAIL_INVALID;
		return "";
	}
}

class Users extends VoicesDB
{
	const TABLE_NAME = 'users';
	
	function setUser( UserRecord $rec )
	{
		$sql = sprintf("INSERT INTO %s (username,password_md5,mail,register_date,user_status) VALUES(:username,:passMd5,:mail,NOW(),:userStatus )",
			self::TABLE_NAME);
		$state = $this->pdo->prepare( $sql );
		$state->execute(
			array( ':username'=>$rec->username, ':passMd5'=>$rec->passwordMd5, ':mail'=>$rec->mail, ':userStatus'=>$rec->userStatus )
			);
		return $state;
	}

	function updateUser( UserRecord $rec )
	{
		if( !$rec->userId ) return false;
		
		$sql = "Update `users` SET";
		$params = array();
		if( $rec->username ) $sql .= " username = :username";
		if( $rec->password_md5 ) $sql .= " password = :passMd5";
		if( $rec->mail ) $sql .= " mail = :mail";
		if( $rec->userStatus ) $sql .= " user_status = :userStatus";
		$sql .= " WHERE user_id = :userId";
		
		$state = $this->pdo->prepare( $sql );
		return $state->execute(
			array( ':userId'=>$rec->userId, ':username'=>$rec->username, ':passMd5'=>$rec->passwordMd5, ':mail'=>$rec->mail, ':userStatus'=>$rec->userStatus )
			);
	}
	
	function authorizeUser( UserRecord $rec )
	{
		$sql = "SELECT * FROM `users` WHERE username LIKE :username AND password_md5 LIKE :passmd5";
		$state = $this->pdo->prepare( $sql );
		$state->execute(
			array( ':username' => $rec->username, ':passmd5' => $rec->passwordMd5 )
			);
		$hash = $state->fetch(PDO::FETCH_ASSOC);
		if( !$hash ) return null;
		
		$sql = "UPDATE `users` SET login_date = NOW() WHERE user_id = :userId";
		$state = $this->pdo->prepare( $sql );
		$state->execute(
			array( ':userId' => $rec->userId )
			);

		return new TeapotUserRecord($hash);
	}

	function getUser( TeapotUserRecord $rec )
	{
		$sql = "SELECT * FROM `users` WHERE";
		if( $rec->userId )
		{
			$sql .= " user_id = :userId";
			$params = array( ':userId' => $rec->userId );
		}
		elseif( $rec->username )
		{
			$sql .= " username LIKE :username";
			$params = array( ':username' => $rec->username );
		}
		elseif( $rec->mail )
		{
			$sql .= " mail LIKE :mail";
			$params = array( ':mail' => $rec->mail );
		}
		else
		{
			return null;
		}
		$state = $this->pdo->prepare( $sql );
		$state->execute( $params );
		$hash = $state->fetch(PDO::FETCH_ASSOC);
		if( !$hash ) return null;
		
		return new TeapotUserRecord($hash);
	}	
}
?>