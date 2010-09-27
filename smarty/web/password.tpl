<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Change password.</title>
	{include file='web/_bg_style.tpl'}
</head>
<body id="bg">
{if $error || $mode == "input"}
	<div align="center">{$error}<br /></div>

	<div align="center">
		<table><tr><td>
			<div align="center">
				Change password !<br /><br />
			</div>

			<form action="{$SECURE_HOME_URL}password.php" method="post">	
				<div align="left">
					<input type="hidden" name="command" value="update">
					Old password: <input type="password" name="password"><br />
					New passwrod: <input type="password" name="password_new"><br />
					Retype password: <input type="password" name="password_retype"><br />
				</div>
				<div align="right">
					<input type="submit" value="Change">
				</div>
			</form>
		</td></tr></table>
	</div>

{elseif $mode == "updated"}
	<div align="center">
		Changed !<br />
		<br />

		<a href="{$HOME_URL}mypage.php">Back mypage</a><br />
		<br />
	</div>
{/if}
{include file='web/_footer.tpl'}
</body>