{*****
	_array
*****}

<table style='border-width: 0'>
{foreach from=$_array item=voice}
	<tr>
	<td style='text-align: left'>
		<a href="{$HOME_URL}voice.php?voice_id={$voice->voiceid}">
			{image_link voice_info=$voice size="icon"}
			Play</a><br/>
		Title: {$voice->title}<br/>
		Artist: {$voice->artist}<br/>
		Count: {$voice->playedCount}<br/>
	</td>
	<td style='text-align: right'>
		<form action="{$HOME_URL}voicelist.php" method="post">
			<input type='hidden' name='playlist_id' value='{$playlist_info->playlistid}'/>
			<input type='hidden' name='command' value='delete'/>
			<input type='hidden' name='voice_id' value='{$voice->voiceid}'/>
			<input type='submit' value='Delete'/>
		</form>
	</td>
	</tr>
	<tr>
	<td colspan='2'>
		<hr width="80%"/>
	</td>
	</tr>
{foreachelse}
	no items.<br/>
{/foreach}
</table>