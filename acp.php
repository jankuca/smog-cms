<?php
define('IN_ACP',true);
define('TEMPLATE_DIRPATH','./styles/.acp/');
require('./config.php');
$tpl->display();
echo(round(microtime(true) - $syslog->start,3) * 1000 . ' ms');
//$syslog->log();
?>