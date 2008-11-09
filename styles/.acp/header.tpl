<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd" xml:lang="en" >
<head>
	<base href="{SITE_ROOT_PATH}" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>{SITE_TITLE}</title>
	<link rel="stylesheet" type="text/css" href="./styles/.acp/media/css/screen.css" />
	<script type="text/javascript" src="./app/lib/js/ajax.js"></script>
	<script type="text/javascript" src="./app/lib/js/jquery.js"></script>
	<script type="text/javascript" src="./app/lib/js/Fat.js"></script>
</head>
<body>
<div id="top"><p><a href="./auth.php?logout">Logout</a>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>{CURRENT_USER.USERNAME}</strong> &mdash; {CURRENT_USER.EMAIL}</p></div>
<div id="header">
	<h1><a href="./acp.php">{SITE_HEADER} / {L_ACP}</a></h1>
</div>
<div id="menubox">
	<ul id="menu">
<loop(MENU_ACP_MAIN)>		<li<if(MENU_ACP_MAIN.ACTIVE)> class="active"</if(MENU_ACP_MAIN.ACTIVE)>><a href="<var(ITEM_LINK)>"><var(ITEM_TEXT)></a></li>
</loop(MENU_ACP_MAIN)>	</ul>

	<ul id="menu_modules">
<loop(MENU_ACP_MODULES)>		<li<if(MENU_ACP_MODULES.ACTIVE)> class="active"</if(MENU_ACP_MODULES.ACTIVE)>><a href="<var(ITEM_LINK)>"><var(ITEM_TEXT)></a></li>
</loop(MENU_ACP_MODULES)>	</ul>
</div>
<if(MENU_ACP_SUB)><ul id="menu_sub">
<loop(MENU_ACP_SUB)>	<li<if(MENU_ACP_SUB.ACTIVE)> class="active"</if(MENU_ACP_SUB.ACTIVE)>><a href="<var(ITEM_LINK)>"><var(ITEM_TEXT)></a></li>
</loop(MENU_ACP_SUB)></ul></if(MENU_ACP_SUB)>
<div id="main"><div id="content">
