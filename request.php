<?php
if(isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) define('REQUEST',$_SERVER['REQUEST_URI']);
elseif(isset($_SERVER['REDIRECT_URL']) && !empty($_SERVER['REDIRECT_URL'])) define('REQUEST',$_SERVER['REDIRECT_URL']);
elseif(isset($_SERVER['REDIRECT_SCRIPT_URL']) && !empty($_SERVER['REDIRECT_SCRIPT_URL'])) define('REQUEST',$_SERVER['REDIRECT_SCRIPT_URL']);
elseif(isset($_SERVER['SCRIPT_URL']) && !empty($_SERVER['SCRIPT_URL'])) define('REQUEST',$_SERVER['SCRIPT_URL']);
else define('REQUEST',false);

if(!REQUEST)
{
	header("HTTP/1.0 404 Not Found");
	die('<h1>HTTP/1.0 404 Not Found</h1>');
}

require('./config.php');
?>