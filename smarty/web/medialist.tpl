<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Media list</title>
</head>
<body>
{$error}

{if !$mode}
<div align="center">
	<div>Title: {$playlist_info->title|escape}</div>
	<br />
	
	{include file='web/_media_array.tpl' _array=$media_array _is_medialist=true}
</div>
{/if}

{include file='web/_footer.tpl'}
</body>
</html>