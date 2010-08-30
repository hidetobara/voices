<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Upload media file.</title>
</head>
<body>
<div align="center">{$error}</div>
{if !$mode}
	Invalid mode !<br/>
{elseif $mode == "input"}
	<form action="{$HOME_URL}upload.php" method="POST" enctype="multipart/form-data"/>
		<input type="hidden" name="command" value="upload"/>
		<input type="hidden" name="user_id" value="1"/>
		
		title: <input type="text" name="title" value="{$upinfo->title|escape}"/><br/>
		artist: <input type="text" name="artist" value="{$upinfo->artist|escape}"/><br/>
		description: <input type="text" name="description" value="{$upinfo->description|escape}"/><br/>
		image: <input type="file" name="image_file"/><br/>
		voice: <input type="file" name="voice_file"/><br/>
		<input type="submit" value="Confirm"/>
	</form>
	
{elseif $mode == "uploaded"}
	Thank you for uploading file.<br/>
	title: {$upinfo->title|escape}<br/>
	artist: {$upinfo->artist|escape}<br/>
	description: {$upinfo->description|escape}<br/>
	
{/if}

{include file='web/_footer.tpl'}
</body>
</html>