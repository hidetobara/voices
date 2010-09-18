<?php
class VoiceWarning extends Exception
{
	public $location;
	public $array;
	function __construct( $message, $array=null )	
	{
		parent::__construct( $message );
		$this->location = $this->getFile() . "#" . $this->getLine();
		$this->array = $array;
	}
}

class VoiceException extends VoiceWarning
{
}

class VoiceMessageException extends VoiceException
{
	function __construct( $id, $array=null )
	{
		$message = CommonMessages::get()->msg( $id );
		parent::__construct( $message, $array );
	}
}
?>