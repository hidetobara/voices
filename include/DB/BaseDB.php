<?php

class VoicesDB
{
	protected $pdo;

	
	function __construct( $options=null )
	{
		$this->pdo = $options['PDO'];
		
		if( !$this->pdo )
		{
			$this->pdo = new PDO(
				sprintf( "mysql:dbname=%s;host=%s", DB_NAME, DB_HOST ),
				DB_USERNAME,
				DB_PASSWORD
				);
			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
	}
}
