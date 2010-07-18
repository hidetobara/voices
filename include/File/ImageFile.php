<?php
require_once( INCLUDE_DIR . "DB/ImageInfo.php" );
require_once( INCLUDE_DIR . "DB/ImageResourceInfo.php" );


class ImageFile
{
	const ERROR_NO_Image_FILE = 'This file is not image';
	
	protected $resourceDb;
	
	private $validContentTypes = array(
		'image/pjpeg' => 'jpg',
		'image/jpeg' => 'jpg',
		'image/png' => 'png' );

	function __construct( $options=null )
	{
		$this->resourceDb = $options['ImageResourceInfoDB'] ? $options['ImageResourceInfoDB'] : new ImageResourceInfoDB();
	}
	
	/*
	 * save voice file from temp file
	 * @return path of voice file
	 */
	function save( Array $src, ImageInfo $info )
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

		$dirDst = IMAGE_DIR . $info->uploadTime->format('Y/m-d/');
		if( !file_exists($dirDst) ) mkdir( $dirDst, 0777, true );
		
		foreach( array(ImageResourceInfo::ICON_SIZE,ImageResourceInfo::WALL_SIZE) as $blockSize )
		{
			$reSize = $this->calcMaxSize( $blockSize, $srcSize );
			$dst = imagecreatetruecolor( $reSize['width'], $reSize['height'] );
			imagecopyresampled( $dst, $img, 0, 0, 0, 0,
				$reSize['width'], $reSize['height'], $srcSize['width'], $srcSize['height'] );
			switch( $type )
			{
				case 'jpg':
					$path = $dirDst . $info->imageid . '_' . $blockSize . '.jpg';
					imagejpeg( $dst, $path, 80 );
					break;
				case 'png':
					$path = $dirDst . $info->imageid . '_' . $blockSize . '.png';
					imagepng( $dst, $path, 80 );
					break;
			}
			$this->resourceDb->newInfo( new ImageResourceInfo(
				array('image_id'=>$info->imageid,'type'=>$type,'size'=>$blockSize,'dst'=>$path) ) );
			imagedestroy( $dst );
		}
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