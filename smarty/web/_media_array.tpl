{*****
	_array
*****}

<table style='border-width: 0'>
{foreach from=$_array key=index item=__media}
	<tr>
	<td style='text-align: left'>
		{include file='web/_media.tpl' _media=$__media}
	</td>
	<td style='text-align: right'>
		<form action="{$HOME_URL}medialist.php" method="post">
			<input type='hidden' name='playlist_id' value='{$playlist_info->playlistid}'>
			<input type='hidden' name='command' value='up'>
			<input type='hidden' name='index' value='{$index}'>
			<input type='submit' value='Up'>
		</form>
		<form action="{$HOME_URL}medialist.php" method="post">
			<input type='hidden' name='playlist_id' value='{$playlist_info->playlistid}'>
			<input type='hidden' name='command' value='delete'>
			<input type='hidden' name='index' value='{$index}'>
			<input type='submit' value='Delete'>
		</form>
		<form action="{$HOME_URL}medialist.php" method="post">
			<input type='hidden' name='playlist_id' value='{$playlist_info->playlistid}'>
			<input type='hidden' name='command' value='down'>
			<input type='hidden' name='index' value='{$index}'>
			<input type='submit' value='Down'>
		</form>
	</td>
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