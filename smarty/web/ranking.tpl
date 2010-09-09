<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Ranking</title>
</head>
<body>
{if $error}
	<div align="center">{$error}<br /></div><br />
	
{elseif $mode == "recent"}
	<div align="center">
		Recent ranking<br />
		<a href="{$HOME_URL}jplayer.php?ranking=recent">Play</a><br />
		<br />

		{include file='web/_media_array.tpl' _array=$media_array}
	</div>
	
{/if}

{include file='web/_footer.tpl'}
</body>
</html>