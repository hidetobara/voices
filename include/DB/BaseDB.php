<?php

class BaseDB
{
	const ERROR_NO_SESSION = 'you do not login ! you need to login.';
	
	protected $pdo;
	
	function __construct( $options=null )
	{
		$this->pdo = $options['PDO'];
		
		if( !$this->pdo )
		{
			$this->pdo = new PDO(
				sprintf( "mysql:dbname=%s;host=%s", DBConfig::$NAME, DBConfig::$HOST ),
				DBConfig::$USERNAME,
				DBConfig::$PASSWORD
				);
			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this->pdo->query("SET NAMES utf8;");
		}
	}
}
