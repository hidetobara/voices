<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Manage account</title>
</head>
<body>
	<div align="center">{$error}</div>
	<div align="center">
		{$message}<br />

		{if $user_info}
			userid: {$user_info->userId}<br />
			username: {$user_info->username}<br />
			<br />
		{/if}

		<table>
			<tr><td>
				New account<br />
				<form action="{$HOME_URL}manage/account.php" method="post">
					<input type="hidden" name="command" value="new">
					<input type="text" name="username"><br />
					<input type="submit" value="MAKE">
				</form>
			</td></tr>
			<tr><td>
				Reset account<br />
				<form action="{$HOME_URL}manage/account.php" method="post">
					<input type="hidden" name="command" value="reset">
					<input type="text" name="username"><br />
					<input type="submit" value="RESET">
				</form>
			</td></tr>
		</table>

	</div>

<div align="center">
	<a href="{$HOME_URL}manage/">Manage top</a>
</div>

</body>
</html>