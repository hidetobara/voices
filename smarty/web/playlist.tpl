<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Playlist</title>
</head>
<body>

{$error}<br/>

{if $mode=="all"}
	{if $step == "new"}
		You made playlist {$playlist_info->title}<br/>
	{/if}

	{***** all *****}
	All playlist<br/>
	{include file="web/_playlist_array.tpl" _array=$playlist_array}
	
	{***** new *****}
	New playlist<br/>
	<form action="{$HOME_URL}playlist.php" method="POST">
		<input type="hidden" name="command" value="new"/>
		Title: <input type="text" name="title"/><br/>
		IDs: <input type="text" name="voice_ids"/><br/>
		
		<input type="submit" value="NEW"/>
	</form>

{elseif $mode=="edit"}
	Edit playlist<br/>
	
	{if !$step}
		<form action="{$HOME_URL}playlist.php" method="POST">
			<input type="hidden" name="command" value="update"/>
			<input type="hidden" name="playlist_id" value="{$playlist_info->playlistid}"/>
			Title: <input type="text" name="title" value="{$playlist_info->title}"/><br/>
			<input type="submit" value="UPDATE"/>
		</form><br/>
		<form action="{$HOME_URL}playlist.php" method="POST">
			<input type="hidden" name="command" value="delete"/>
			<input type="hidden" name="playlist_id" value="{$playlist_info->playlistid}"/>
			<input type="hidden" name="title" value="{$playlist_info->title}"/>
			<input type="submit" value="DELETE"/>
		</form><br/>
	{else}
		Title: {$playlist_info->title}<br/>
		{$message}<br/>
	{/if}

{/if}

{include file="web/_footer.tpl"}
</body>