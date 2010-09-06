{*****
	_array
	_is_medialist -> $playlist_info
	_is_uploadlist
*****}

<table style='border-width: 0'>
{foreach from=$_array key=index item=voice}
	<tr>
	<td style='text-align: left'>
		{if !$voice->isVisible}
			Don't play !
		{elseif $voice->type == 'voice'}
			<a href="{$HOME_URL}jplayer.php?mid={$voice->mediaid}">
				{image_link _media_info=$voice size="icon"}
				Play</a><br/>
			Title: {$voice->title|escape}<br/>
			Artist: {$voice->artist|escape}<br/>
			{if $voice->playedCount}Count: {$voice->playedCount}<br/>{/if}
		{/if}
	</td>
	{if $_is_medialist}
	<td style='text-align: right'>
		<form action="{$HOME_URL}medialist.php" method="post">
			<input type='hidden' name='playlist_id' value='{$playlist_info->playlistid}'/>
			<input type='hidden' name='command' value='delete'/>
			<input type='hidden' name='index' value='{$index}'/>
			<input type='submit' value='Delete'/>
		</form>
	</td>
	{/if}
	{if $_is_uploadlist && $voice->isVisible}
	<td style='text-align: right'>
		<form action="{$HOME_URL}uploadlist.php" method="post">
			<input type='hidden' name='voice_id' value='{$voice->voiceid}'/>
			<input type='hidden' name='command' value='deleting'/>
			<input type='submit' value='Delete'/>
		</form>
	</td>
	{/if}
	</tr>
	<tr>
	<td colspan='2'>
		<hr width="100%"/>
	</td>
	</tr>
{foreachelse}
	no items.<br/>
{/foreach}
</table>