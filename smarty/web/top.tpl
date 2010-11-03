<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title></title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
<div align="center">
	<br />
	<div>Put once, play anywhere !</div>
	{if $session_userid}
		<div><a href="{$HOME_URL}jplayer.php?program=RandomPlayAll">Try random play</a></div>
	{/if}
	<br />
	<div><a href='./session.php'>Login/Logout</a></div>
	{if $session_userid}
		<div><a href='./playlist.php'>Playlist</a></div>
		<div><a href='./mypage.php'>My page</a></div>
	{/if}
</div>

</body>
</html>
