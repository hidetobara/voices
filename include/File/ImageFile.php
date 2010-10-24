<?php
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );


class ImageFile
{
	const ERROR_NO_Image_FILE = 'This file is not image';
	
	private $validContentTypes = array(
		'image/pjpeg' => 'jpg',
		'image/jpeg' => 'jpg',
		'image/png' => 'png' );

	protected $imageDb;
	
	function __construct( $options=null )
	{
		$this->imageDb = $opt['ImageInfoDB'] ? $opt['ImageInfoDB'] : new ImageInfoDB();
	}
	
	/*
	 * save voice file from temp file
	 * @return path of voice file
	 */
	function save( $userid, Array $src )
	{
		$pathSrc = $src['tmp_name'];
		$type = $this->validContentTypes[ $src['type'] ];
		if( !$type ) throw new VoiceException(CommonMessages::get()->msg('NOT_IMAGE_FILE'));

		switch( $type )
		{
			case 'jpg':
				$img = imagecreatefromjpeg( $pathSrc );	break;
			case 'png':
				$img = imagecreatefrompng( $pathSrc );	break;
		}
		$srcSize = array( 'height'=>imagesy( $img ), 'width'=>imagesx( $img ) );

		$info = $this->imageDb->newInfo(
			new ImageInfo( array('user_id'=>$userid, 'type'=>$type) ) );
		if( !$info ) throw new VoiceException(CommonMessages::get()->msg('UNKNOWN'));

		foreach( array(ImageInfo::ICON_SIZE,ImageInfo::WALL_SIZE) as $blockSize )
		{
			$path = $info->getFilePath( $blockSize );
			$dirDst = dirname( $path );
			if( !is_dir($dirDst) ) mkdir( $dirDst, 0777, true );
			
			$reSize = $this->calcMaxSize( $blockSize, $srcSize );
			$dst = imagecreatetruecolor( $reSize['width'], $reSize['height'] );
			imagecopyresampled( $dst, $img, 0, 0, 0, 0,
				$reSize['width'], $reSize['height'], $srcSize['width'], $srcSize['height'] );
			switch( $type )
			{
				case 'jpg':
					imagejpeg( $dst, $path, 80 );
					break;
				case 'png':
					imagepng( $dst, $path, 80 );
					break;
			}
			imagedestroy( $dst );
		}
		return $info;
	}
	
	protected function calcMaxSize( $max, $size )
	{
		if( $size['height'] > $size['width'] )
		{
			return array(
				'height' => $max,
				'width' => ceil($size['width']*$max/$size['height']) );
		}
		else
		{
			return array(
				'height' => ceil($size['height']*$max/$size['width']),
				'width' => $max );
		}
	}
	
	static function type2ContentType( $type )
	{
		$type = strtolower( $type );
		switch( $type )
		{
			case 'jpg':	return 'image/jpeg';
			case 'png':	return 'image/png';
		}
		return null;
	}
}
?>