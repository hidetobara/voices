<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Session</title>
</head>
<body>
{if $logined}
	<div align="center">"
		you are logined.<br />
		<form action='{$SECURE_HOME_URL}session.php' METHOD='POST'>
			<input type='hidden' name='command' value='logout'/>
			<input type='submit' value='Logout'/>
		</form>
	</div>

{else}
	<div align="center">
		try login.<br />
		{if $error}
			{$error}<br />
		{/if}
		<form action='{$SECURE_HOME_URL}session.php' METHOD='POST'>
			<input type='hidden' name='command' value='login'/>
			username: <input type='text' name='username'/><br/>
			password: <input type='password' name='password'/><br/>
			<input type='submit' value='Login'/>
		</form>	
	</div>

{/if}

{include file='web/_footer.tpl'}
</body>
</html>