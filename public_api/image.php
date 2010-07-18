<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "web/CommonMessages.php" );
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );
require_once( INCLUDE_DIR . "DB/ImageResourceInfo.php" );
require_once( INCLUDE_DIR . "File/ImageFile.php" );


class DownloadImage
{
	protected $rDb;
	protected $rInfo;
	
	function __construct( $options=null )
	{
		$this->rDb = $options['ImageResourceInfoDB'] ? $options['ImageResourceInfoDB'] : new ImageResourceInfoDB();
	}
	
	function run()
	{
		try
		{
			$this->initialize();
			$this->handle();
		}
		catch(Exception $ex)
		{	
			header( "Content-Type: text/html; charset=UTF-8" );
			print $ex->getMessage();
		}
	}
	
	function initialize()
	{
		$imageid = $_REQUEST['id'];
		$size = ImageResourceInfo::name2size( $_REQUEST['size'] );
		if( !$imageid || !$size ) throw new VoiceException(CommonMessages::get()->msg('INVALID_PARAMETER'));
		
		$this->rInfo = $this->rDb->getInfo( $imageid, $size );
		if( !$this->rInfo ) throw new VoiceException(CommonMessages::get()->msg('NO_IMAGE_INFO'));
	}
	function handle()
	{
		$path = $this->rInfo->dst;
		if( !file_exists($path) ) throw new VoiceException(CommonMessages::get()->msg('NO_FILE'));
		
		$ct = ImageFile::type2ContentType( $this->rInfo->type );
		if( $ct ) header("Content-type: {$ct}");
		header('Content-Length: ' . filesize($path));
		readfile($path);
	}
}
$instance = new DownloadImage();
$instance->run();
?>