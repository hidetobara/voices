{*****
	_array
*****}

{foreach from=$_array item=play}
	<div style='text-align: left'>
		<img src="" height="32" width="32"/>
		<a href="{$HOME_URL}voicelist.php?playlist_id={$play->playlistid}">{$play->title}</a><br/>
	</div>
	<div style='text-align: right'>
		<a href="{$HOME_URL}playlist.php?command=edit&amp;playlist_id={$play->playlistid}">Edit.</a><br/>
	</div>
	<hr width="80%"/>
{foreachelse}
	no playlists.<br/>
{/foreach}
