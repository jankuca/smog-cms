<?php
require_once('./app/layers/syslog.layer.php');
$syslog->start = $time;

$dir = dir('./app/lib/');
while($file = $dir->read())
{
	if($file == '.' || $file == '..') continue;
	if(!preg_match('#([a-zA-Z0-9-]+)\.(lib|class)\.php#is',$file)) continue;
	
	ob_start();
	if(include_once('./app/lib/' . $file)) $syslog->success('(root)','include_once()','./app/lib/' . $file);
	else $syslog->success('(root)','include_once()','./app/lib/' . $file,ob_get_contents());
	ob_end_clean();
}
unset($dir,$file);

rm_magicquotes();

require_once('./app/layers/langs.layer.php');
require_once('./app/layers/sqlite.layer.php');
require_once('./app/layers/template.layer.php');
require_once('./app/layers/modules.layer.php');
require_once('./app/layers/core.layer.php');

// Init the connection to the database
$sqlite = new SQLite('./data/sql/.database.sqlite','scms_',0777);
// Init the template system
$tpl = new Template();

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
				if((int) $item->assign) $tpl->assignVar(strtoupper($item->name),$cfg['etc'][$item->module][$item->name]);
				break;
			case('timestamp'):
				$cfg['etc'][$item->module][$item->name] = strtotime($item->value);
				if((int) $item->assign) $tpl->assignVar(strtoupper($item->name),formatDateTime($cfg['etc'][$item->module][$item->name]));
				break;
			default:
				$cfg['etc'][$item->module][$item->name] = $item->value;
				if((int) $item->assign) $tpl->assignVar(strtoupper($item->name),$cfg['etc'][$item->module][$item->name]);
				break;
		}
	}
}

if(defined('REQUEST'))
{
	$sql = new SQLObject();
	if($sql->query("
SELECT request_id,request_regexp,request_uri,request_target,request_ifc
FROM " . $sql->table('requests') . "
ORDER BY request_order ASC"))
	{
		foreach($sql->fetch() as $request)
		{
			if((boolean) $request->request_regexp === false)
			{
				if(REQUEST == $request->request_uri || REQUEST . '/' == $request->request_uri)
				{
					define('LOC',$request->request_target);
					define('IFC',$request->request_ifc);
				}
			}
			elseif(preg_match($request->request_uri,REQUEST) || preg_match($request->request_uri,REQUEST . '/'))
			{
				define('LOC',$request->request_target);
				define('IFC',$request->request_ifc);
			}
		}
	}
	unset($sql);
	
	if(!defined('LOC')) { header('Location: ../index.php'); }
}

// Configure the template system
if(defined('TEMPLATE_DIRPATH')) $tpl->dirpath = TEMPLATE_DIRPATH;
else $tpl->dirpath = './styles/' . $cfg['etc']['core']['site_style'] . '/';

// Init the language system
if(defined('IN_SYS') && IN_SYS) $lang = new Langs('./langs/' . $cfg['etc']['core']['site_lang'] . '/');
else $lang = new Langs('./langs/' . $cfg['etc']['core']['acp_lang'] . '/');

define('SITE_ROOT_PATH',$cfg['etc']['core']['SITE_ROOT_PATH']);

$tpl->assignVar('HTTP_REFERER',$_SERVER['HTTP_REFERER']);

// Init the modules
//$mod = new Modules();


// redirect
if(defined('REQUEST') && defined('LOC'))
{
	if(defined('IFC')) define(IFC,true);
	$mod = new Modules();
	include(LOC);
}
else $mod = new Modules();
?>