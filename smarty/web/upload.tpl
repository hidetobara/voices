<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Upload media file.</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error}
	<div align="center">
		{$error}<br />
	</div>
	
{elseif !$mode}
	<div align="center">
		Invalid mode !<br />
	</div>
	
{elseif $mode == "input"}
	<div align="center">
		<table><tr><td>
			The limit of upload file size is 32M !<br />
			<br />
			
			<form action="{$HOME_URL}upload.php" method="POST" enctype="multipart/form-data"/>
				<div align="left">
					<input type="hidden" name="command" value="upload"/>					
					title: <input type="text" name="title" value="{$upinfo->title|escape}"/><br />
					artist: <input type="text" name="artist" value="{$upinfo->artist|escape}"/><br />
					description: <input type="text" name="description" value="{$upinfo->description|escape}"/><br />
					image: <input type="file" name="image_file"/><br />
					voice: <input type="file" name="voice_file"/><br />
				</div>
				<div align="right">
					<input type="submit" value="Confirm"/>
				</div>
			</form>
		</td></tr></table>
	</div>
	
{elseif $mode == "uploaded"}
	<div align="center">
		<table><tr><td>
			<div align="left">
				Thank you for uploading file.<br />
				title: {$upinfo->title|escape}<br />
				artist: {$upinfo->artist|escape}<br />
				description: {$upinfo->description|escape}<br />
			</div>
			<div align="right">
				<a href="{$HOME_URL}uploadlist.php">&gt;&gt;&gt;Uploadlist</a><br />
				<a href="{$HOME_URL}upload.php">&gt;&gt;&gt;Upload again</a><br />
			</div>
		</td></tr></table>
	</div>
{/if}

{include file='web/_footer.tpl'}
</body>
</html>