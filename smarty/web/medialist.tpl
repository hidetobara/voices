<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Media list</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error}<div align="center">{$error}</div>{/if}

<div align="center">
	<div>Title: {$playlist_info->title|escape}</div>
	<br />
	
	{include file='web/_media_array.tpl' _array=$media_array}
</div>

{include file='web/_footer.tpl'}
</body>
</html>