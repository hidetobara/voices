<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>My page</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error}
	<div align="center">
		{$error}<br />
		<a href="{$HOME_URL}session.php">Login page</a><br />
	</div>
	
{else}
	<div align="center">
		<table>
			<tr><td>
				<b>Menu</b><br />
				<a href="{$HOME_URL}upload.php">Upload music</a><br />
				<a href="{$HOME_URL}playlist.php">Playlist</a><br />
				<a href="{$HOME_URL}uploadlist.php">Uploadlist</a><br />
				<a href="{$HOME_URL}password.php">Change password</a><br />
			</td></tr>
		</table>
	</div>
	<br />
	
{/if}
{include file='web/_footer.tpl'}
</body>
</html>