<?php
class Modules
{
	public $modules = array();
	
	public function __construct()
	{
		$sql = new SQLObject();
		if($sql->query("SELECT filename,core,seq FROM " . $sql->table('modules') . " WHERE (active = 1 AND core = 1) ORDER BY seq ASC"))
		{
			$core = $sql->fetch();
			if($sql->query("SELECT filename,core,seq FROM " . $sql->table('modules') . " WHERE (active = 1 AND core = 0) ORDER BY seq ASC"))
			{
				$modules = array_merge($core,$sql->fetch());
				foreach($modules as $module)
				{
					if((int) $module->core == 1) $path = './app/modules/' . $module->filename;
					else $path = './modules/' . $module->filename;
					
					$GLOBALS['MODULE_NAME'] = str_replace('-','_',preg_replace('#^([a-z-_]+)\.mod\.php$#is','$1',$module->filename));
					
					if(include_once($path))
					{
						core::s('syslog')->success('Modules','__construct()',$path);
						
						if(
							defined('IN_ACP') && IN_ACP
							&& isset($_GET['c'],$_GET['module'])
							&& $_GET['c'] == 'config'
							&& $_GET['module'] == $GLOBALS['MODULE_NAME']
							&& method_exists($this->modules[$GLOBALS['MODULE_NAME']],'_module_config_load')
						){
							core::s('tpl')->addTpl('config-' . $GLOBALS['MODULE_NAME']);
							$this->modules[$GLOBALS['MODULE_NAME']]->_module_config_load();
						}
					}
					else
					{
						core::s('syslog')->error('Modules','__construct()',$path,'Cannot load the module!');
					}
				}
			}
		}
	}
}
?>