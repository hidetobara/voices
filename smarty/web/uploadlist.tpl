<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Upload list</title>
</head>
<body>
{if $error}
	<div align="center">
		{$error}<br />
	</div>

{elseif $mode == 'deleting'}
	<div align="center">
		Delete OK ?<br />
		Title: {$target_voice_info->title}<br />
		Artist: {$target_voice_info->artist}<br />
		<form action="{$HOME_URL}uploadlist.php" method="post">
			<input type='hidden' name='voice_id' value='{$target_voice_info->voiceid}'/>
			<input type='hidden' name='command' value='delete'/>
			<input type='submit' value='Delete'/>
		</form>
	</div>
	
{else}
	<div align="center">
		<div>My media: {$my_total_size} KB / {$size_limit} KB</div>
		<br />
		
		{include file='web/_media_array.tpl' _array=$my_voice_infos _is_uploadlist=true}
	</div>

{/if}

{include file='web/_footer.tpl'}
</body>
</html>