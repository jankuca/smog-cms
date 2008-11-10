<?php
require_once('./app/layers/langs.layer.php');
require_once('./app/layers/tpl.layer.php');
require_once('./app/layers/modules.layer.php');
require_once('./app/layers/core.layer.php');

// Load the cms configuration
$sql = new SQLObject();
if($sql->query("
SELECT name,value,assign,type,module
FROM " . $sql->table('config') . "
WHERE (name NOT LIKE 'info:%')
UNION
SELECT name,value,assign,type,module
FROM " . $sql->table('config') . "
WHERE (name LIKE 'info:%')"))
{
	foreach($sql->fetch() as $item)
	{
		switch((string) $item->type)
		{
			case('integer'):
				$cfg['etc'][$item->module][$item->name] = (int) $item->value;
				if((int) $item->assign) TPL::add(strtoupper($item->name),$cfg['etc'][$item->module][$item->name]);
				break;
			case('timestamp'):
				$cfg['etc'][$item->module][$item->name] = strtotime($item->value);
				if((int) $item->assign) TPL::add(strtoupper($item->name),formatDateTime($cfg['etc'][$item->module][$item->name]));
				break;
			default:
				$cfg['etc'][$item->module][$item->name] = $item->value;
				if((int) $item->assign) TPL::add(strtoupper($item->name),$cfg['etc'][$item->module][$item->name]);
				break;
		}
	}
}

// Configure the template system
if(defined('TEMPLATE_DIRPATH')) TPL::$dirpath = TEMPLATE_DIRPATH;
else TPL::$dirpath = './styles/' . $cfg['etc']['core']['site_style'] . '/';

// Init the language system
if(defined('IN_SYS') && IN_SYS) Langs::$dirpath = './langs/' . $cfg['etc']['core']['site_lang'] . '/';
else Langs::$dirpath = './langs/' . $cfg['etc']['core']['acp_lang'] . '/';

define('SITE_ROOT_PATH',$cfg['etc']['core']['SITE_ROOT_PATH']);

TPL::add('HTTP_REFERER',$_SERVER['HTTP_REFERER']);

// Init the modules
Modules::load();

?>