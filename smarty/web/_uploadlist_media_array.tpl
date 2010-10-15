{*****
	_array
	_is_medialist -> $playlist_info
	_is_uploadlist
*****}

<table style='border-width: 0'>
{foreach from=$_array key=index item=__media}
	<tr>
	<td style='text-align: left'>
		{include file='web/_media.tpl' _media=$__media}
	</td>
	<td style='text-align: right'>
		<form action="{$HOME_URL}uploadlist.php" method="post">
			<input type='hidden' name='voice_id' value='{$__media->voiceid}'/>
			<input type='hidden' name='command' value='deleting'/>
			<input type='submit' value='Delete'/>
		</form>
		<form action="{$HOME_URL}uploadlist.php" method="post">
			<input type='hidden' name='voice_id' value='{$__media->voiceid}'/>
			<input type='hidden' name='command' value='editing'/>
			<input type='submit' value='Edit'/>
		</form>
	</td>
	</tr>
	<tr>
	<td colspan='2'>
		<hr width="100%"/>
	</td>
	</tr>
{foreachelse}
	no items.<br /><br />
{/foreach}
</table>