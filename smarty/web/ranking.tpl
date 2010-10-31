<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Ranking</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error}
	<div align="center">{$error}<br /></div><br />
	
{elseif $mode == "recent"}
	<div align="center">
		Recent ranking<br />
		<a href="{$HOME_URL}jplayer.php?program=RecentRanking">Play</a><br />
		<br />

		<table style='border-width: 0'>
			{foreach from=$media_array key=index item=media}
				<tr>
				<td style='text-align: left'>
					{include file='web/_media.tpl' _media=$media}
				</td>
				</tr>
				<tr>
				<td colspan='1'>
					<hr width="100%"/>
				</td>
				</tr>
			{foreachelse}
				<tr><td>No items.</td></tr>
			{/foreach}
		</table>
	</div>
	
{/if}

	<div align="center">
		<div>and <a href="{$HOME_URL}jplayer.php?program=RandomPlay">random play</a></div>
		<br />
	</div>

{include file='web/_footer.tpl'}
</body>
</html>