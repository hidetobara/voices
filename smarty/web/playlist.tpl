<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Playlist</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error}<div align="center">{$error}</div>{/if}

{if $mode=="all"}
<div align="center">
	{if $step == "new"}
		You made playlist {$playlist_info->title|escape}<br />
	{/if}

	{***** all *****}
	All playlist<br />
	<br />
	{include file="web/_playlist_array.tpl" _array=$playlist_array}
	
	{***** new *****}
	<table><tr><td>
		<div align="center">New playlist</div>
		<form action="{$HOME_URL}playlist.php" method="POST">
			<div align="left">
				<input type="hidden" name="command" value="new" />
				Title: <input type="text" name="title"/><br />
			</div>
			<div align="right">
				<input type="submit" value="NEW" />
			</div>
		</form>
	</td></tr></table>

</div>

{elseif $mode=="edit"}
<div align="center">
	Edit playlist<br />
	
	{if !$step}
	<table><tr><td>
		<form action="{$HOME_URL}playlist.php" method="POST" enctype="multipart/form-data"/>
			<div align="left">
				{image_link _playlist_info=$playlist_info size="icon"}<br />
				<input type="hidden" name="command" value="update"/>
				<input type="hidden" name="playlist_id" value="{$playlist_info->playlistid}"/>
				Title: <input type="text" name="title" value="{$playlist_info->title|escape}"/><br/>
				Image: <input type="file" name="image_file"/><br/>
			</div>
			<div align="right">
				<input type="submit" value="UPDATE"/>
			</div>
		</form>
		<form action="{$HOME_URL}playlist.php" method="POST">
			<div align="left">
				<input type="hidden" name="command" value="delete"/>
				<input type="hidden" name="playlist_id" value="{$playlist_info->playlistid}"/>
				<input type="hidden" name="title" value="{$playlist_info->title|escape}"/>
			</div>
			<div align="right">
				<input type="submit" value="DELETE"/>
			</div>
		</form>
	</td></tr></table>

	{elseif $step == "updated"}
		Update !<br />
		{image_link _playlist_info=$playlist_info size="icon"}
		Title: {$playlist_info->title|escape}<br/>
		
	{elseif $step == "deleted"}
		Deleted !<br />
		Title: {$playlist_info->title|escape}<br/>
		
	{/if}
</div>

{/if}

{include file="web/_footer.tpl"}
</body>
</html>