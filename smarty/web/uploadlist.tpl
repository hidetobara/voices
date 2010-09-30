<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Uploadlist</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error}
	<div align="center">{$error}<br /></div>

{elseif $mode == 'deleting'}
	<div align="center">
		<table><tr><td>
			<div align="center">Delete OK ?</div>

			<form action="{$HOME_URL}uploadlist.php" method="post">
				<div align="left">
					{image_link _media_info=$target_voice_info size="icon"}<br />
					Title: {$target_voice_info->title}<br />
					Artist: {$target_voice_info->artist}<br />
					<input type='hidden' name='voice_id' value='{$target_voice_info->voiceid}'/>
					<input type='hidden' name='command' value='delete'/>
				</div>
				<div align="right">
					<input type='submit' value='Delete'/>
				</div>
			</form>
		</td></tr></table>
		
	</div>
	
{elseif $mode == 'editing'}
	<div align="center">
		<table><tr><td>
			<form action="{$HOME_URL}uploadlist.php" method="post" enctype="multipart/form-data"/>
				<div align="left">
					{image_link _media_info=$target_voice_info size="icon"}<br />
					<input type='hidden' name='voice_id' value='{$target_voice_info->voiceid}'/>
					<input type='hidden' name='command' value='edit'/>
					Title: <input type='text' name='title' value='{$target_voice_info->title}'><br />
					Artist: <input type='text' name='artist' value='{$target_voice_info->artist}'><br />
					Image: <input type="file" name='image_file'><br />
				</div>
				<div align="right">
					<input type='submit' value='Edit'/>
				</div>
			</form>
		</td></tr></table>
	</div>
	
{else}
	<div align="center">
		<div>My media: {$my_total_size} KB / {$size_limit} KB</div>
		<br />
		
		{if $paging->pageCount > 1}
			<div>
			{foreach from=$paging->pages item=p}
				{if $p != 1} | {/if}
				{if $p == $paging->currentPage} {$p} {else} <a href="{$HOME_URL}uploadlist.php?page={$p}">{$p}</a> {/if}
			{/foreach}
			</div>
		{/if}
		
		{include file='web/_uploadlist_media_array.tpl' _array=$my_voice_infos}

		{if $paging->pageCount > 1}
			<div>
			{foreach from=$paging->pages item=p}
				{if $p != 1} | {/if}
				{if $p == $paging->currentPage} {$p} {else} <a href="{$HOME_URL}uploadlist.php?page={$p}">{$p}</a> {/if}
			{/foreach}
			</div>
		{/if}		
	</div>

{/if}

{include file='web/_footer.tpl'}
</body>
</html>