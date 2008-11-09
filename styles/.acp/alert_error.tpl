<html>
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>...</title>
	<link rel="stylesheet" type="text/css" href="./styles/.acp/media/css/authbox.css" />
</head>
<body>
<div class="alert_error"><div>
	{ALERT_MESSAGE}<br /><br />
	<ul class="links">
		<li><a href="javascript:history.go(-1);">{L_BACKLINK}</a></li>
	</ul>
	<ul class="links">
		<li><a href="./">{L_INDEX}</a></li>
		<if(LOGGED)><li><a href="./acp.php">{L_ACP_HOME}</a></li></if(LOGGED)>
	</ul>
</div></div>
<div id="footer">Smog CMS</div>
</body>
</html>