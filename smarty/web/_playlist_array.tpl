{*****
	_array
*****}

<table style='border-width: 0'>
{foreach from=$_array item=play}
	<tr>
		<td style='text-align: left'>
			<a href="{$HOME_URL}jplayer.php?playlist_id={$play->playlistid}">
				{image_link _playlist_info=$play size="icon"}
				Play
			</a><br />
			Title: {$play->title}<br />
		</td>
		<td style='text-align: right'>
			<a href="{$HOME_URL}medialist.php?playlist_id={$play->playlistid}">&gt;&gt;&gt;View List</a><br/>
			<a href="{$HOME_URL}playlist.php?command=edit&amp;playlist_id={$play->playlistid}">&gt;&gt;&gt;Edit</a><br/>
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr width="100%"/></td>
	</tr>
{foreachelse}
	<tr><td>No playlists.</td></tr>
{/foreach}
</table>
