<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Media list</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error}<div align="center">{$error}</div>{/if}

<div align="center">
	<div>Title: {$playlist_info->title|escape}</div>
	<div><a href="{$HOME_URL}jplayer.php?playlist_id={$playlist_info->playlistid}">Play playlist</a></div>
	<br />
	
	<table style='border-width: 0'>
	{foreach from=$media_array key=index item=media}
		<tr>
		<td style='text-align: left'>
			{include file='web/_media.tpl' _media=$media}
		</td>
		<td style='text-align: right'>
			<form action="{$HOME_URL}medialist.php" method="post">
				<input type='hidden' name='playlist_id' value='{$playlist_info->playlistid}'>
				<input type='hidden' name='index' value='{$index}'>
				<select name="command" value="select a move action">
					<option value="top">Top</option>
					<option value="up">Up</option>
					<option value="down">Down</option>
					<option value="bottom">Bottom</option>
					<option value="delete">Remove</option>
				</select>
				<input type='submit' value='Move'>
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

</div>

{include file='web/_footer.tpl'}
</body>
</html>