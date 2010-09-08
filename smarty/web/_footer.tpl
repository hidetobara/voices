

<div style="text-align:center">
	{if !$session_userid}
		<a href="{$HOME_URL}">Top</a> |
		<a href="{$HOME_URL}session.php">Login !</a> |
		<a href="{$HOME_URL}ranking.php">Ranking</a>
	{else}
		<a href="{$HOME_URL}">Top</a> |
		<a href="{$HOME_URL}ranking.php">Ranking</a> |
		<a href="{$HOME_URL}playlist.php">Playlist</a> |
		<a href="{$HOME_URL}mypage.php">My page</a>
	{/if}
</div>
