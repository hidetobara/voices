<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Media list</title>
</head>
<body>
{if $error}<div align="center">{$error}</div>{/if}

<div align="center">
	<div>Title: {$playlist_info->title|escape}</div>
	<br />
	
	{include file='web/_media_array.tpl' _array=$media_array _is_medialist=true}
</div>

{include file='web/_footer.tpl'}
</body>
</html>