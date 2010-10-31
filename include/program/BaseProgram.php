<?php
require_once( INCLUDE_DIR . "DB/MediaInfo.php" );
require_once( INCLUDE_DIR . "File/FileCache.php" );

abstract class BaseProgram
{
	protected $userid;
	protected $memory;
	//protected $name;
	
	function __construct( $userid, $mem=null, $opt=null )
	{
		$this->userid = $userid;
		$this->memory = $mem;
	}
	
	abstract function getInfos();
	abstract function currentInfo();
	abstract function nextInfo();
}