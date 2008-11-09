<html>
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>{L_AUTHBOX_TITLE}</title>
	<link rel="stylesheet" type="text/css" href="./styles/.acp/media/css/authbox.css" />
</head>
<body>
<div id="bar" style="border-top: 24px solid #FBB;"></div>
<h1>{L_AUTHBOX_TITLE}</h1>
<div id="authbox"></div>
<div id="footer">Smog CMS</div>
<script type="text/javascript" src="./app/lib/js/hash.js"></script>
<script type="text/javascript"><!--
document.getElementById('bar').style.borderTopColor = '#DFA';

document.getElementById('authbox').innerHTML = 
'<form action="{AUTHBOX_ACTION}" method="post" onsubmit="return(secure_form(this,\'auth_salt\',\'auth_password\',\'auth_password_hash\',\'auth_submit\'));">'+
'<div>'+
'	<label for="auth_username">{L_USERNAME}:</label>'+
'	<input type="text" class="text" name="auth_username" id="auth_username" /><br />'+
'	<label for="auth_username">{L_PASSWORD}:</label>'+
'	<input type="password" class="password" name="auth_password" id="auth_password" /><br />'+
'	<div class="bottom">'+
'		<span class="checkbox"><input type="checkbox" name="auth_save" value="1" id="auth_save" /> <label for="auth_save">{L_AUTH_SAVE}</label></span>'+
'		<input type="hidden" name="auth_challenge" value="{AUTH_CHALLENGE}" />'+
'		<input type="hidden" name="auth_password_hash" value="" id="auth_password_hash" />'+
'		<input type="hidden" name="auth_salt" value="{AUTH_SALT}" id="auth_salt" />'+
'		<span class="button" style="width: 106px; background: url(\'./styles/.acp/media/images/button-auth.png\') no-repeat;"><input type="submit" value="{L_LOGIN}" id="auth_submit" /></span>'+
'	</div>'+
'</div>'+
'</form>';

function secure_form(formNode,inputSalt,inputPassword,inputPasswordHash,inputSubmit)
{
	var saltNode = document.getElementById(inputSalt);
	var passwordNode = document.getElementById(inputPassword);
	var passwordHashNode = document.getElementById(inputPasswordHash);
	var submitNode = document.getElementById(inputSubmit);
	if(
		typeof formNode == 'object'
		&& typeof saltNode == 'object'
		&& typeof passwordNode == 'object'
		&& typeof passwordHashNode == 'object'
		&& typeof submitNode == 'object'
	){
		passwordHashNode.value = hex_sha256(hex_sha256(passwordNode.value) + saltNode.value);
		passwordNode.disabled = true;
		saltNode.disabled = true;
		submitNode.disabled = true;
		formNode.submit();
		passwordNode.disabled = false;
	}
	else alert('{L_AUTH_INSECURE}');
	return(false);
}
--></script>
</body>
</html>