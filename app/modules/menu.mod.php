<?php
class module_menu
{
	/*public $menu = array(
		'sys' => array(
			'main' => array()
		),
		'acp' => array(
			'global' => array(),
			'modules' => array(),
			'sub' => array()
		)
	);*/
	
	public function __construct()
	{
		$this->menu = new stdClass();
		$this->menu->acp = new stdClass();
		$this->menu->acp->main = new MenuObject('MENU_ACP_MAIN');
		$this->menu->acp->modules = new MenuObject('MENU_ACP_MODULES');
		$this->menu->acp->sub = new MenuObject('MENU_ACP_SUB');
		$this->menu->sys = new stdClass();
		$this->menu->sys->main = new MenuObject('MENU_SYS_MAIN');
		
		if(defined('IN_SYS') && IN_SYS)
		{
		
		}
		elseif(defined('IN_ACP') && IN_ACP)
		{	
			$this->menu->acp->modules->addItem('./acp.php?c=menu','{L_MODULE_MENU}',array('ACTIVE' => (isset($_GET['c']) && $_GET['c'] == 'menu')));
			
			core::s('tpl')->addQueue('
			$mod->modules[\'menu\']->menu->acp->main->make();
			$mod->modules[\'menu\']->menu->acp->modules->make();
			
			if(!$mod->modules[\'menu\']->menu->acp->sub->numItems()) $this->assignCond(\'MENU_ACP_SUB\',false);
			else $this->assignCond(\'MENU_ACP_SUB\',true);
			$mod->modules[\'menu\']->menu->acp->sub->make();',
			false);
			//echo('in menu module - on line 42 :D :: http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING'] != '' ? '?'.$_SERVER['QUERY_STRING'] : ''));
		}
	}
}

class MenuObject
{
	public $loopKey; # Key reprezentating the menu in the Template system
	public $varKeys = array(
		'ITEM_LINK',
		'ITEM_TEXT'
	); # Keys reprezentating the items' values when the menu loops
	private $items; # Menu items formatated for the Template system
	
	public function __construct($loopKey,$varKeys = false)
	{
		$this->loopKey = (string) $loopKey;
		if($varKeys)
			$this->varKeys = (array) $varKeys;
	}
	
	public function addItem($link,$text,$conds = array())
	{
		# Adds item to the menu.
		#
		# @param string $link
		# @param string $text
		# @param array $conds
		
		$this->items[] = array(
			"{$this->varKeys[0]}" => (string) $link,
			"{$this->varKeys[1]}" => (string) $text,
			'conds' => (array) $conds
		);
	}
	
	public function numItems()
	{
		# Returns count of the added items
		return(count($this->items));
	}
	
	public function make()
	{
		core::s('tpl')->assignLoop($this->loopKey,$this->items);
	}
}

$this->modules['menu'] = new module_menu();
?>