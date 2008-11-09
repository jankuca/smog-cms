<html>
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>...</title>
	<link rel="stylesheet" type="text/css" href="./styles/.acp/media/css/authbox.css" />
	<if(BACKLINK)><script type="text/javascript"><!--
window.onload = function()
{
	setTimeout('window.location.href = \'{BACKLINK}\';',2000);
}
	--></script></if(BACKLINK)>
</head>
<body>
<div class="alert_success"><div>
	{ALERT_MESSAGE}<br /><br />
	<img src="./styles/.acp/media/images/throbber.gif" alt="[...]" class="throbber" /><br />
	<if(BACKLINK)><ul class="links">
		<li><a href="{BACKLINK}">{BACKLINK_TEXT}</a></li>
	</ul></if(BACKLINK)>
	<ul class="links">
		<li><a href="./">{L_INDEX}</a></li>
		<if(LOGGED)><li><a href="./acp.php">{L_ACP_HOME}</a></li></if(LOGGED)>
	</ul>
</div></div>
<div id="footer">Smog CMS</div>
</body>
</html>