<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Session</title>
</head>
<body>
{if $logined}
	you are logined.<br/>
	<form action='{$HOME_URL}session.php' METHOD='POST'>
		<input type='hidden' name='command' value='logout'/>
		<input type='submit' value='Logout'/>
	</form>
{else}
	try login.<br/>
	{if $message}
		{$message}<br/>
	{/if}
	<form action='{$HOME_URL}session.php' METHOD='POST'>
		<input type='hidden' name='command' value='login'/>
		username: <input type='text' name='username'/><br/>
		password: <input type='password' name='password'/><br/>
		<input type='submit' value='Login'/>
	</form>	

{/if}

</body>
</html>