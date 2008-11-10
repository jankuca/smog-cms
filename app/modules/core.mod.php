<?php
class module_core
{
	public function _module_config_load()
	{
		$cfg = core::s('cfg');
		
		TPL::add('SITE_TITLE','{L_SITE_CONFIG} &mdash; {SITE_HEADER} / {L_ACP}');
		
		$sql = new SQLObject();
		if($sql->query("
SELECT module,name,value,type
FROM " . $sql->table('config') . "
WHERE (module = 'core')"))
		{
			foreach($sql->fetch() as $item)
			{
				switch($item->type)
				{
					case('integer'): $value = (int) $item->value; break;
					case('string'): $value = (string) $item->value; break;
					case('float'): $value = (float) $item->value; break;
					case('timestamp'): $value = strtotime((string) $item->value); break;
					case('boolean'):
						if((int) $item->value == 0) $value = false;
						else $value = true;
						TPL::cond('MODULE_CONFIG:' . strtoupper($item->name),$value);
						break;
					default: $value = $item->value; break;
				}
				TPL::add('MODULE_CONFIG:' . strtoupper($item->name),$value);
			}
		}
		

		$dir = dir('./langs/');
		while($file = $dir->read())
		{
			if($file == '.' || $file == '..') continue;
			$langs[] = $file;
		}
		sort($langs);
		$f_langs = array();
		foreach($langs as $lang)
			$f_langs[] = array(
				'LANG_CODENAME' => $lang,
				'conds' => array(
					'LANG_ACTIVE' => ($lang == $cfg['etc']['core']['site_lang']) ? true : false
				)
			);
		TPL::assignAsLoop('MODULE_CONFIG:SITE_LANG',$f_langs);
		$f_langs = array();
		foreach($langs as $lang)
			$f_langs[] = array(
				'LANG_CODENAME' => $lang,
				'conds' => array(
					'LANG_ACTIVE' => ($lang == $cfg['etc']['core']['acp_lang']) ? true : false
				)
			);
		TPL::assignAsLoop('MODULE_CONFIG:ACP_LANG',$f_langs);
		
		$dir = dir('./styles/');
		while($file = $dir->read())
		{
			if($file == '.' || $file == '..' || $file == '.acp') continue;
			$styles[] = $file;
		}
		sort($styles);
		$f_styles = array();
		foreach($styles as $style)
		{
			$info = array();
			
			$path = './styles/' . $style . '/style.cfg';
			if(file_exists($path))
			{
				$data = simplexml_load_file($path);
				foreach($data->children() as $item)
				{
					switch($item['name'])
					{
						case('name'): $info['name'] = $item['value']; break;
						case('author'): $info['author'] = $item['value']; break;
						case('author_link'): $info['author_link'] = $item['value']; break;
						case('preview'): $preview = true; $info['preview'] = './styles/' . $style . '/' . $item['value']; break;
					}
				}
			}
			
			if(!isset($info['name'])) $info['name'] = $style;
			if(!isset($info['author'])) $info['author'] = '{L_UNKNOWN}';
			if(!isset($info['preview'])) { $preview = false; $info['preview'] = './styles/.acp/media/images/style_preview.png'; }
			
			if($style == $cfg['etc']['core']['site_style']) TPL::add('MODULE_CONFIG:SITE_STYLE_NAME',(strlen($info['name']) > 24) ? mb_substr($info['name'],0,24,'UTF-8').'...' : $info['name']);
			
			$f_styles[] = array(
				'STYLE_CODENAME' => $style,
				'STYLE_NAME' => $info['name'],
				'STYLE_AUTHOR' => $info['author'],
				'STYLE_AUTHOR_LINK' => $info['author_link'],
				'STYLE_PREVIEW' => $info['preview'],
				'conds' => array(
					'STYLE_ACTIVE' => ($style == $cfg['etc']['core']['site_style']) ? true : false,
					'STYLE_AUTHOR_LINK' => (isset($info['author_link'])) ? true : false,
					'STYLE_PREVIEW' => $preview
				)
			);
		}
		
		TPL::assignAsLoop('MODULE_CONFIG:SITE_STYLE',$f_styles);
	}
}

Modules::$modules->{$GLOBALS['MODULE_NAME']} = new module_core();
Langs::load('core');

if(
	(defined('IN_SYS') && IN_SYS)
	|| (defined('IN_ACP') && IN_ACP)
){
	TPL::addTpl('header');
	if(!isset($_GET['c'])) TPL::addTpl('homepage');
	TPL::modify('self::addTpl(\'footer\');self::$output .= self::$loadedTpls[\'footer\'];');
}

if(defined('IN_ACP') && IN_ACP)
{
	Modules::$modules->menu->menu->acp->main->addItem(
		'./acp.php',
		'{L_ACP_HOME}',
		array('ACTIVE' => (!isset($_GET['c'])))
	);
	Modules::$modules->menu->menu->acp->main->addItem(
		'./',
		'{L_INDEX}',
		array('ACTIVE' => false)
	);
	if(permission('core','modules','show'))
		Modules::$modules->menu->menu->acp->main->addItem(
			'./acp.php?c=modules',
			'{L_MODULES}',
			array('ACTIVE' => (isset($_GET['c']) && $_GET['c'] == 'modules'))
		);
	if(permission('core','config','edit'))
		Modules::$modules->menu->menu->acp->main->addItem(
			'./acp.php?c=config&amp;module=core',
			'{L_SITE_CONFIG}',
			array('ACTIVE' => (isset($_GET['c'],$_GET['module']) && $_GET['c'] == 'config' && $_GET['module'] == 'core'))
		);
	
	if(!isset($_GET['c']))
	{
		TPL::add('SITE_TITLE','{SITE_HEADER} / {L_ACP}');
	}
	else
	{
		switch($_GET['c'])
		{
/*			case('config'):
				if(isset($_GET['module']) && $_GET['module'] == $MODULE_NAME)
					$this->modules[$MODULE_NAME]->_module_config_load();
				break;*/
			
			case('modules'):
				TPL::addTpl('modules');
				TPL::add('SITE_TITLE','{L_MODULES} &mdash; {SITE_HEADER} / {L_ACP}');
				
				$codenames = array();
				
				$f_modules_core = array();
				$f_modules_additional = array();
				$sql = new SQLObject();
				if($sql->query("
SELECT filename,seq,core,name,description,version,active FROM " . $sql->table('modules') . " WHERE (core = 1)
UNION
SELECT filename,seq,core,name,description,version,active FROM " . $sql->table('modules') . " WHERE (core = 0)
ORDER BY seq ASC"))
				{
					foreach($sql->fetch() as $module)
					{
						if((int) $module->core == 1)
						{
							$f_modules_core[] = array(
								'MODULE_CODENAME' => str_replace('.mod.php','',$module->filename),
								'MODULE_NAME' => $module->name,
								'MODULE_DESCRIPTION' => $module->description,
								'MODULE_PATH' => './app/modules/' . $module->filename,
								'MODULE_ICON' => './app/modules/' . str_replace('.mod.php','.icon.png',$module->filename),
								'conds' => array(
									'ACTIVE' => (boolean)(int) $module->active
								)
							);
						}
						else
						{
							$f_modules_additional[] = array(
								'MODULE_CODENAME' => str_replace('.mod.php','',$module->filename),
								'MODULE_NAME' => $module->name,
								'MODULE_DESCRIPTION' => $module->description,
								'MODULE_PATH' => './modules/' . $module->filename,
								'MODULE_ICON' => './modules/' . str_replace('.mod.php','.icon.png',$module->filename),
								'conds' => array(
									'ACTIVE' => (boolean)((int) $module->active)
								)
							);
						}
						
						$codenames[(int) $module->core][] = $module->filename;
					}
				}
				
				$f_modules_available = array();
				$dir = dir('./modules/');
				while($file = $dir->read())
				{
					if(preg_match('#^([a-z-_]+)\.mod\.php$#is',$file,$arr))
					{
						if(!in_array($arr[1] . '.mod.php',$codenames[0]))
						{
							if(file_exists('./install/modules/' . $arr[1] . '.cfg'))
							{
								$module_info = explode('-----',file_get_contents('./install/modules/' . $arr[1] . '.cfg'));
								$module_info = $module_info[0];
								preg_match_all('#([a-z]+):(.*?);#is',$module_info,$arr2);
								foreach($arr2[1] as $i => $variable) $info[$variable] = $arr2[2][$i];
							}
							else
							{
								$info = array(
									'filename' => $arr[1] . '.mod.php',
									'name' => 'Unknown module',
									'description' => 'Unknown module',
									'version' => 'N/A',
									'author' => '<em>Unsigned</em>'
								);
							}
							$f_modules_available[] = array(
								'MODULE_CODENAME' => $arr[1],
								'MODULE_NAME' => $info['name'],
								'MODULE_DESCRIPTION' => $info['description'],
								'MODULE_VERSION' => $info['version'],
								'MODULE_AUTHOR' => $info['author'],
								'MODULE_ICON' => './modules/' . $arr[1] . '.icon.png',
							);
						}
					}
				}
				
				if(count($f_modules_core)) TPL::cond('MODULES_CORE',true);
				else TPL::cond('MODULES_CORE',false);
				TPL::assignAsLoop('MODULES_CORE',$f_modules_core);
				
				if(count($f_modules_additional) != 0) TPL::cond('MODULES_ADDITIONAL',true);
				else TPL::cond('MODULES_ADDITIONAL',false);
				TPL::assignAsLoop('MODULES_ADDITIONAL',$f_modules_additional);

				if(count($f_modules_available) != 0) TPL::cond('MODULES_AVAILABLE',true);
				else TPL::cond('MODULES_AVAILABLE',false);
				TPL::assignAsLoop('MODULES_AVAILABLE',$f_modules_available);
				
				break;
		}
	}
}

if(defined('IN_AJAXREQUEST') && IN_AJAXREQUEST)
{
	if(isset($_GET['c'],$_GET['function']) && $_GET['c'] == 'modules')
	{
		switch($_GET['function'])
		{
			case('install'):
				if(isset($_GET['module_codename']) && preg_match('#^([a-zA-Z0-9_-]+)$#is',$_GET['module_codename']))
				{
					if(!permission('core','modules','install'))
						die("\n".'  <em>Terminating..</em> You do not have the required permissions!'."\n");
					
					if(file_exists('./install/modules/' . $_GET['module_codename'] . '.install.php'))
					{
						$module_info = explode('-----',file_get_contents('./install/modules/' . $_GET['module_codename'] . '.cfg'));
						$module_files = $module_info[1];
						$module_dirs = $module_info[2];
						$module_info = $module_info[0];
						
						preg_match_all('#([a-z]+):(.*?);#is',$module_info,$arr2);
						foreach($arr2[1] as $i => $variable) $info[$variable] = $arr2[2][$i];
						echo('# Loading module info... <strong>OK</strong>'."\n");
						
						preg_match_all('#file:(.*?):(0|1);#is',$module_files,$arr2);
						foreach($arr2[1] as $i => $variable) $files[(int) $arr2[2][$i]][] = $arr2[1][$i];
						echo('# Loading list of required and optional files for installation... <strong>OK</strong>'."\n");
						
						$dirs = explode("\r\n",$module_dirs);unset($dirs[0]);
						echo('# Loading list of required directoris for installation... <strong>OK</strong>'."\n\n");
						
						// check required files
						$error = false;
						echo('# Checking whether all the required files do exist...'."\n");
						foreach($files[1] as $file)
						{
							if(file_exists($file)) echo('  [x] <strong>' . $file . '</strong>'."\n");
							else { $error = true; echo('  [ ] <em>' . $file . '</em>'."\n"); }
						}
						
						if($error) die("\n".'  <em>Terminating</em>... Some required files do not exist!'."\n");
						else echo('  <strong>OK</strong>... All the required files do exist.'."\n\n");
						
						// check optional files
						$error = false;
						echo('# Checking whether the optional files do exist...'."\n");
						foreach($files[0] as $file)
						{
							if(file_exists($file)) echo('  [x] <strong>' . $file . '</strong>'."\n");
							else { $error = true; echo('  [ ] <em>' . $file . '</em>'."\n"); }
						}
						
						if($error) echo('  <em>Error</em>... Some optional files do not exist!'."\n\n");
						else echo('  <strong>OK</strong>... All the optional files do exist.'."\n\n");
						
						// check required dirs
						$error = false;
						echo('# Checking whether all the required directories do exist and are writable...'."\n");
						foreach($dirs as $dir)
						{
							if(file_exists($dir) && is_dir($dir) && is_writable($dir)) echo('  [x] <strong>' . $dir . '</strong>'."\n");
							else { $error = true; echo('  [ ] <em>' . $dir . '</em>'."\n"); }
						}
						
						if($error) die("\n".'  <em>Terminating</em>... Some required directories do not exist or are not writable!'."\n");
						else echo('  <strong>OK</strong>... All the required directories do exist and are writable.'."\n\n");
						
						
						echo('----------------------------------------------------------------------------------------------------'."\n\n");
						echo('  [x] <strong>./install/modules/' . $_GET['module_codename'] . '.install.php</strong>'."\n\n");
						echo('# Starting the installation process...'."\n\n");
						$history = array();
						
						function undo_installation($history)
						{
							echo("\n".'# Cleaning after installation...');
							foreach($history as $item) eval($item);
							die(' <strong>OK</strong>');
						}
						include_once('./install/modules/' . $_GET['module_codename'] . '.install.php');
						die("\n".'  <strong>OK</strong>... Installation successful'."\n");
					}
					else
					{
						echo("\n".'----------------------------------------------------------------------------------------------------'."\n\n");
						echo('  [ ] <em>./install/modules/' . $_GET['module_codename'] . '.install.php</em>'."\n");
						die('  <em>Terminating</em>... The codename of the module is not set or is invalid!'."\n");
					}
				}
				else
				{
					echo("\n".'----------------------------------------------------------------------------------------------------'."\n\n");
					die('  <em>Terminating</em>... The codename of the module is not set or is invalid!'."\n");
				}
				break;
			case('uninstall'):
				echo("\n".'----------------------------------------------------------------------------------------------------'."\n\n");
				if(isset($_GET['module_codename']) && preg_match('#^([a-zA-Z0-9_-]+)$#is',$_GET['module_codename']))
				{
					if(!permission('core','modules','uninstall'))
						die("\n".'  <em>Terminating..</em> You do not have the required permissions!'."\n");
					
					if(file_exists('./install/modules/' . $_GET['module_codename'] . '.uninstall.php'))
					{
						echo('  [x] <strong>./install/modules/' . $_GET['module_codename'] . '.uninstall.php</strong>'."\n\n");
						echo('# Starting the uninstallation process...'."\n\n");
						include_once('./install/modules/' . $_GET['module_codename'] . '.uninstall.php');
						die("\n".'  <strong>OK</strong>... Uninstallation successful'."\n");
					}
					else
					{
						echo('  [ ] <em>./install/modules/' . $_GET['module_codename'] . '.uninstall.php</em>'."\n");
						die('  <em>Terminating</em>... The uninstallation file does not exist!'."\n");
					}
				}
				else die("\n".'  <em>Terminating</em>... The codename of the module is not set or is invalid!'."\n");
				break;
			
			case('activate'):
				if(isset($_GET['module_codename']) && preg_match('#^([a-zA-Z0-9_-]+)$#is',$_GET['module_codename']))
				{
					if(!permission('core','modules','activate'))
						die('ERROR');
					
					$sql = new SQLObject();
					if($sql->exec("UPDATE " . $sql->table('modules') . " SET active = 1 WHERE (filename = '" . $_GET['module_codename'] . ".mod.php')"))
						echo('OK');
					else echo('ERROR');
				}
				else echo('ERROR');
				break;
			case('deactivate'):
				if(isset($_GET['module_codename']) && preg_match('#^([a-zA-Z0-9_-]+)$#is',$_GET['module_codename']))
				{
					if(!permission('core','modules','activate'))
						die('ERROR');
					
					$sql = new SQLObject();
					if($sql->exec("UPDATE " . $sql->table('modules') . " SET active = 0 WHERE (filename = '" . $_GET['module_codename'] . ".mod.php')"))
						echo('OK');
					else echo('ERROR');
				}
				else echo('ERROR');
				break;
		}
	}
	elseif(isset($_GET['c'],$_GET['module']) && $_GET['c'] == 'config' && $_GET['module'] != '')
	{
		if(isset($_POST['config']))
		{
			if(permission('core','config','edit'))
			{
				$sql = new SQLObject();
				$query = '';
				foreach($_POST['config'] as $module => $variables)
				{
					foreach($variables as $name => $value)
					{
						$query .= "UPDATE " . $sql->table('config') . " SET value = '" . $sql->escape($value) . "' WHERE (module = '" . $sql->escape($module) . "' AND lower(name) = lower('" . $sql->escape($name) . "'));";
					}
				}
				
				if($query != '')
				{
					if($sql->exec($query)) echo('OK');
					else echo('ERROR');
				}
				else echo('OK');
			}
			else echo('AUTH');
		}
	}
}
?>