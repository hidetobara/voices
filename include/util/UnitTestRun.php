<?php
if (!defined("PHPUnit2_MAIN_METHOD"))
{
	define("PHPUnit2_MAIN_METHOD", "Test::main");
}
require_once "PHPUnit2/Framework/IncompleteTestError.php";
require_once "PHPUnit2/Framework/TestCase.php";
require_once "PHPUnit2/Framework/TestSuite.php";
require_once "PHPUnit2/TextUI/TestRunner.php";


function runUnitTest( $testClassName, $classPath )
{
	$path = DATA_DIR . 'unit_test/' . $testClassName . '.dat';
	$dir = dirname($path);
	if( !file_exists( $dir ) ) mkdir( $dir, 0777, true );
	
	$suite = new PHPUnit2_Framework_TestSuite( $testClassName );
	$result = PHPUnit2_TextUI_TestRunner::run( $suite, $path );

	$text = file_get_contents( $path );
	$data = unserialize( $text );
	$lines = array();
	foreach( $data as $func => $targets )
	{
		$target = $targets[ $classPath ];
		if( !$target )
		{
			$classPath2 = str_replace( '/','\\',$classPath );
			$target = $targets[ $classPath2 ];
		}
		if( !$target ) continue;
		foreach( $target as $line => $value )
		{
			if( $lines[ $line ] < 1 ) $lines[ $line ] = $value; else $lines[ $line ]++;
		}
	}
	ksort( $lines );
	//var_dump( $lines );

	$file = fopen( $classPath, "r" );
	if( !$file )
	{
		print "NOT FOUND ! {$classPath}\n";
		return;
	}

	print "<hr>\n<html>\n<style type='text/css'><!--\n";
	print "\t.no { background-color: #FF8888; }\n";
	print "\t.passed { background-color: #BBBBFF; }\n";
	print "--></style>\n<body>\n";
	$index = 1;
	while( $line = fgets($file) )
	{
		$line = rtrim( $line );
		$line = str_replace( "\t", "&nbsp;&nbsp;", $line );
		
		if( $lines[ $index ] > 0 ) print "<div class='passed'>{$index}: ";
		else if( $lines[ $index ] == -1 ) print "<div class='no'>{$index}: ";
		else print "<div>{$index}: ";
		
		print $line . "</div>\n";

		$index++;
	}
	fclose( $file );
	print "</body></html>";
}

?>