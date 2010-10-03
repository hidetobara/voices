<?php
require_once( INCLUDE_DIR . "DB/BaseDB.php" );

class TempKey
{
	public $userid;
	public $tempKey;
	public $updateDate;

	public function __construct( $obj=null )
	{
		if( $obj ) $this->copyRecord( $obj );
	}
	
	public function copyRecord( $obj )
	{
		if( is_array( $obj ) )
		{
			$this->userid = $obj['user_id'];
			$this->updateDate = $obj['update_date'];
			
			if( $obj['temp_key'] )
			{
				$this->tempKey = $obj['temp_key'];
			}
			else
			{
				$this->makeTempKey();
			}
		}
	}
	
	public function makeTempKey()
	{
		$this->tempKey = md5( $this->userid . ":" . time() . ":" . rand(0,1000) );
	}
	
	public function isAlive( $aliveDay )
	{
		$alive = sprintf( "+%d day", $aliveDay );
		
		$deadline = new DateTime( $this->updateDate );
		$deadline->modify( $alive );
		$now = new DateTime();
		if( $deadline < $now ) return false;
		return true;
	}
}

class TempKeyDB extends BaseDB
{
	const TABLE_NAME = 'voices_temp_key';
	
	function updateTempKey( TempKey $rec )
	{
		if( !$rec->userid ) return false;
	
		$sql = sprintf( "UPDATE `%s` SET `temp_key`=:tempKey, `update_date`=NOW() WHERE `user_id`=:userId",
			self::TABLE_NAME );
		$state = $this->pdo->prepare( $sql );
		$state->execute(
			array( ':userId' => $rec->userid, ':tempKey' => $rec->tempKey )
			);

		if( $state->rowCount() == 0 )
		{
			$sql = sprintf( "INSERT INTO `%s` (user_id,temp_key,update_date) VALUES(:userId,:tempKey,NOW())",
				self::TABLE_NAME );
			$state = $this->pdo->prepare( $sql );
			$state->execute(
				array( ':userId' => $rec->userid, ':tempKey' => $rec->tempKey )
				);
		}
		return true;
	}
	
	function authorizeTempKey( TempKey $rec )
	{
		if( !$rec->userid ) return null;
		
		$sql = sprintf( "SELECT * FROM `%s` WHERE `user_id` LIKE :userId AND `temp_key` LIKE :tempKey",
			self::TABLE_NAME );
		$state = $this->pdo->prepare( $sql );
		$state->execute(
			array( ':userId' => $rec->userid, ':tempKey' => $rec->tempKey )
			);
		$hash = $state->fetch(PDO::FETCH_ASSOC);
		if( !$hash ) return null;
		
		return new TempKey( $hash );
	}
}
?>