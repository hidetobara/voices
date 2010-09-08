<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>My page</title>
</head>
<body>
{if $error}
	<div align="center">
		{$error}<br />
		<a href="{$HOME_URL}session.php">Login page</a><br />
	</div>
	
{else}
	<div align="center">
		<a href="{$HOME_URL}upload.php">Upload music</a><br />
		<a href="{$HOME_URL}playlist.php">Playlist</a><br />
		<a href="{$HOME_URL}uploadlist.php">Uploadlist</a><br />
		<br />
	</div>
	
{/if}
{include file='web/_footer.tpl'}
</body>
</html>