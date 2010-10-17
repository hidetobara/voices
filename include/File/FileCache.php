<?php

class FileCache
{
	function set( $key, array $data, DateTime $expire=null )
	{
		if( is_null($key) ) return;
		
		$json = json_encode( $data );
		file_put_contents( $this->path($key), $expire->format("Y-m-d H:i:s") . "\n" . $json );
	}
	
	function get( $key )
	{
		if( is_null($key) ) return null;
		if( !is_file($this->path($key)) ) return null;
		
		$file = fopen( $this->path($key), "r" );
		$line = fgets( $file );
		$expire = new DateTime( $line );
		$now = new DateTime();
		if( $expire < $now )
		{
			fclose( $file );
			return null;
		}
		
		$line = fgets( $file );
		fclose( $file );
		$data = json_decode( $line, true );
		return $data;
	}
	
	private function path( $key )
	{
		return DATA_DIR . "/cache/" . $key;
	}
}