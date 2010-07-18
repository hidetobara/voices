<?php

class CommonMessages
{	
	private static $instance;
	public static $lang = 'ja';
	public static function get()
	{
		if( !self::$instance )
		{
			self::$instance = new self();
			self::$instance->load( self::$lang );
		}
		return self::$instance;
	}

	private $messages;
	private $languages = array('en'=>1,'ja'=>2);
	
	function load( $lang )
	{
		$col = $this->languages[ $lang ];
		if( !$col ) $col = 1;

		$this->messages = array();
		
		$path = DATA_DIR . 'messages/common.csv';
		$fin = fopen( $path, 'r' );
		while( $line = fgets($fin) )
		{
			$line = rtrim( $line );
			$cells = mb_split( ';', $line );
			if( count($cells) <= 1 ) continue;
			
			$key = $cells[0];
			$value = $cells[$col];
			if( !$value ) continue;
			
			$this->messages[ $key ] = $value;
		}
		fclose( $fin );
	}
	
	function msg( $key )
	{
		$value = $this->messages[ $key ];
		return $value ? $value : '...';
	}
}
?>