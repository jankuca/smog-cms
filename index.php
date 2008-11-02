<?php
//if($_SERVER['REMOTE_ADDR'] != '86.49.84.27') die('Smog CMS is under construction!');

define('IN_SYS',true);
//define('TEMPLATE_DIRPATH','./styles/default/');
require('./config.php');

$tpl->assignLoop(
	'MENU',
	array(
		array(
			'MENU_LINK' => './',
			'MENU_TEXT' => 'Homepage',
			'MENU_ID' => '1',
			'conds' => array(
				'ACTIVE' => true,
				'LAST' => false,
				'SUBMENU' => false
			)
		),
		array(
			'MENU_LINK' => './documentation',
			'MENU_TEXT' => 'Documentation',
			'MENU_ID' => '2',
			'conds' => array(
				'ACTIVE' => false,
				'LAST' => false,
				'SUBMENU' => true
			)
		),
		array(
			'MENU_LINK' => './screenshots',
			'MENU_TEXT' => 'Screenshots',
			'conds' => array(
				'ACTIVE' => false,
				'LAST' => false,
				'SUBMENU' => false
			)
		),
		array(
			'MENU_LINK' => './faq',
			'MENU_TEXT' => 'FAQ',
			'conds' => array(
				'ACTIVE' => false,
				'LAST' => false,
				'SUBMENU' => false
			)
		),
		array(
			'MENU_LINK' => './download',
			'MENU_TEXT' => 'Download',
			'conds' => array(
				'ACTIVE' => false,
				'LAST' => false,
				'SUBMENU' => false
			)
		),
		array(
			'MENU_LINK' => './forum',
			'MENU_TEXT' => 'Forum',
			'conds' => array(
				'ACTIVE' => false,
				'LAST' => true,
				'SUBMENU' => false
			)
		)
	)
);

$tpl->assignLoop(
	'MENU.SUBMENU.2',
	array(
		array(
			'MENU_SUBMENU_LINK' => './craap.php',
			'MENU_SUBMENU_TEXT' => 'Craap!',
			'conds' => array(
				'ACTIVE' => true,
				'LAST' => true
			)
		)
	)
);

$tpl->display();
echo(round(microtime(true) - $syslog->start,3) * 1000 . ' ms');
/*echo('<br style="clear:both" />Output generated in <strong>' . round((microtime(true) - $start) * 1000) . ' ms</strong>.');
echo('<br /><br />');*/
//$syslog->log();
/*$sql = new SQLObject();
$sql->exec("UPDATE lsg_config SET value = 'Smog CMS' WHERE (name = 'SITE_HEADER')");
echo($sql->error);
//$sql->exec("INSERT INTO lsg_config (name,value,assign,type,module) VALUES ('SITE_HEADER','LSG-CMS',1,'string','core')");*/
?>