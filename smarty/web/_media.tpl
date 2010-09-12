{*****
	_media: MediaInfo
*****}

{if !$_media->isVisible}
	Don't play !
{elseif $_media->type == 'voice'}
	<a href="{$HOME_URL}jplayer.php?mid={$_media->mediaid}">
		{image_link _media_info=$_media size="icon"}
		Play</a><br/>
	Title: {$_media->title|escape}<br/>
	Artist: {$_media->artist|escape}<br/>
	{if $_media->playedCount}Count: {$_media->playedCount}<br/>{/if}
{/if}
