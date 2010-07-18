<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Voicelist</title>
</head>
<body>
{$error}

{if !$mode}
	Title: {$playlist_info->title}<br/>
	{include file='web/_voicelist_array.tpl' _array=$voice_array}

{elseif $mode == 'select'}
	Title: {$voice_info->title}<br/>
	<form action='{$home}voicelist.php'>
		<input type='hidden' name='command' value='add'/>
		<input type='hidden' name='voice_id' value='{$voice_info->voiceid}'/>
		<select name='playlist_id'>
		{foreach from=$playlist_array item=play}
			<option value='{$play->playlistid}'>{$play->title}</option>
		{/foreach}
		</select>
		<input type='submit' value='Add'/>
	</form>

{/if}

{include file='web/_footer.tpl'}
</body>
</html>