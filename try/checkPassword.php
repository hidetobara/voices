<?php
require_once( '../configure.php' );
loadLocalConf( 'secrect.conf' );

if( !$argv[1] )
{
	print "generate hash for this system.\n";
	print "input password !\n";
	exit;
}
else
{
	$md5 = md5( $argv[1] . PASSWORD_SEED );
	print "md5 = {$md5}\n";
}