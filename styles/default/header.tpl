<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<base href="{SITE_ROOT_PATH}" />
	<link rel="start" href="./" title="{SITE_HEADER}" />
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="cs" />
	<meta name="robots" content="noindex,nofollow" />
	<meta name="googlebot" content="noindex,nofollow,noarchive" />
	
	<title>Smog CMS</title>
	<meta name="description" content="Modern, fast, reliable, modulable, ... content management system." />
	<meta name="keywords" content="smog,smog cms,cms,scms,s cms,look smog,content management system" />
	
	<meta name="designer" content="Look Smog; http://www.looksmog.eu; look.smog (at) gmail.ccom" />
	<meta name="author" content="Look Smog; http://www.looksmog.eu; look.smog (at) gmail.com" />
	<meta name="copyright" content="Look Smog" />
	
	<link rel="stylesheet" media="screen,projection,tv" type="text/css" href="./styles/default/media/css/screen.css" />
	<!--[if lt IE 7]><link rel="stylesheet" media="screen,projection,tv" type="text/css" href="./styles/default/media/css/screen_ie6.css" /><![endif]-->
	<link rel="shortcut icon" type="image/x-icon" href="./favicon.png" />
	
	<!--[if lt IE 7]><script defer="defer" type="text/javascript" src="pngfix.js"></script><![endif]-->
</head>
<body>
<div id="web">
	<h1><a href="./">{SITE_HEADER}<span></span></a></h1>
	<p id="slogan">{SITE_SLOGAN}</p>
	
	<ul id="menu">
<loop(MENU)>		<li>
			<a href="<var(MENU_LINK)>"><var(MENU_TEXT)></a>
<if(MENU.SUBMENU)>			<!--ul>
<loop(MENU.SUBMENU.<var(MENU_ID)>)>				<li<if(MENU.SUBMENU.<var(MENU_ID)>.ACTIVE)> class="active"</if(MENU.SUBMENU.<var(MENU_ID)>.ACTIVE)>><a href="<var(MENU_SUBMENU_LINK)>"><var(MENU_SUBMENU_TEXT)></a></li>
</loop(MENU.SUBMENU.<var(MENU_ID)>)>			</ul-->
</if(MENU.SUBMENU)>		</li>
</loop(MENU)>	</ul>
	
	<div id="content">
		<div id="content-top"></div>
