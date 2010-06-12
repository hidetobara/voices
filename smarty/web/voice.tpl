<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Upload voice file.</title>
</head>
<body>
<div align="center">{$error}</div>
{if !$mode}
	Invalid mode !<br/>
{elseif $mode == "input" || $mode == "edit"}
	<form action="{$HOME_URL}upload.php" method="POST" enctype="multipart/form-data"/>
		<input type="hidden" name="mode" value="{$next_mode}"/>
		<input type="hidden" name="user_id" value="1"/>
		
		title: <input type="text" name="title" value="{$upinfo->title}"/><br/>
		artist: <input type="text" name="artist" value="{$upinfo->artist}"/><br/>
		description: <input type="text" name="description" value="{$upinfo->description}"/><br/>
		image: <input type="file" name="thumbnail_file"/><br/>
		voice: <input type="file" name="voice_file"/><br/>
		<input type="submit" value="Confirm"/>
	</form>
	
{elseif $mode == "upload"}
	Thank you for uploading file.<br/>
	title: {$upinfo->title}<br/>
	artist: {$upinfo->artist}<br/>
	description: {$upinfo->description}<br/>
	
{/if}
</body>
</html>