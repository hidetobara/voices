<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Media list</title>
</head>
<body>
{$error}

{if !$mode}
	Title: {$playlist_info->title}<br/>
	{include file='web/_medialist_array.tpl' _array=$media_array}
{/if}

{include file='web/_footer.tpl'}
</body>
</html>