<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>voice edit</title>
</head>
<body>
{if $error}<div align="center">{$error}</div>{/if}

{if $target_voice}
<div align="center">
	<table>
		<tr>
			<td colspan='2'>{image_link _media_info=$target_voice size="wall"}<br/></td>
		</tr>
		<tr>
			<td colspan='2'>{$target_voice->title|escape}</td>
		</tr>
		<tr>
			<td colspan='2'>{$target_voice->artist|escape}</td>
		</tr>
		<tr>
			<td colspan='2'>
				{if !$mode}
					Register current item:<br />
					<form action="{$HOME_URL}media_edit.php" method="POST">
						<input type="hidden" name="command" value="register_playlist" />
						<input type="hidden" name="mid" value="{$target_voice->mediaid}" />
						<select name="playlist_id">
						{foreach from=$playlist_array item=play}
							<option value="{$play->playlistid}">{$play->title|escape}</option>
						{/foreach}
						</select>
						<input type="submit" value="register" />
					</form>
				{elseif $mode == "registered_playlist"}
					Add it into {$target_playlist->title|escape}.<br />
				{/if}
			</td>
		</tr>
		<tr>
			<td colspan='2'><hr /></td>
		</tr>
		<tr>
			<td colspan='2' style="text-align: right">
				<a href="{$HOME_URL}jplayer.php?mid={$target_voice->mediaid}">&gt;&gt;&gt;Player</a>
			</td>
		</tr>
	</table>
</div>

{/if}

{include file='web/_footer.tpl'}
</body>
</html>