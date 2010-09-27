<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Session</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $logined}
	<div align="center">
			you are logined.<br />
			<form action='{$SECURE_HOME_URL}session.php' METHOD='POST'>
				<input type='hidden' name='command' value='logout'/>
				<input type='submit' value='Logout'/>
			</form>
	</div>

{else}
	<div align="center">
		<table><tr><td>
			<div align="center">
				Try login.<br />	
				{if $error}{$error}<br />{/if}
				<br />
			</div>
		
			<form action='{$SECURE_HOME_URL}session.php' METHOD='POST'>
				<div align="left">
					<input type='hidden' name='command' value='login'/>
					username: <input type='text' name='username'/><br />
					password: <input type='password' name='password'/><br />
				</div>
				<div align="right">
					<input type='submit' value='Login'/>
				</div>
			</form>
		</td></tr></table>
	</div>

{/if}

{include file='web/_footer.tpl'}
</body>
</html>