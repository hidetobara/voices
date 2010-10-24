<?php
require_once( "../configure.php" );
require_once( INCLUDE_DIR . "VoiceException.php" );
require_once( INCLUDE_DIR . "web/CommonMessages.php" );
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );
require_once( INCLUDE_DIR . "File/ImageFile.php" );


class DownloadImage
{
	protected $imageDb;
	protected $imageInfo;
	protected $size;
	
	function __construct( $options=null )
	{
		$this->imageDb = $options['ImageInfoDB'] ? $options['ImageInfoDB'] : new ImageInfoDB();
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
		$this->size = ImageInfo::name2size( $_REQUEST['size'] );
		if( !$imageid || !$this->size ) throw new VoiceException(CommonMessages::get()->msg('INVALID_PARAMETER'));
	
		$this->imageInfo = $this->imageDb->getInfo( $imageid );
		if( !$this->imageInfo ) throw new VoiceException(CommonMessages::get()->msg('NO_IMAGE_INFO'));
	}
	function handle()
	{
		$path = $this->imageInfo->getFilePath( $this->size );
		if( !file_exists($path) ) throw new VoiceException(CommonMessages::get()->msg('NO_FILE'));
		
		$ct = ImageFile::type2ContentType( $this->imageInfo->type );
		if( $ct ) header("Content-type: {$ct}");
		header('Content-Length: ' . filesize($path));
		readfile($path);
	}
}
$instance = new DownloadImage();
$instance->run();
?>