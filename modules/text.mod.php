<?php
class module_text
{

}

Modules::$modules->text = new module_text();
if(defined('IN_SYS') && IN_SYS)
{
	TPL::modify('
	preg_match_all(\'#{TEXT:([a-zA-Z0-9-_]+)}#s\',self::$output,$arr);
	if(count($arr[1]))
	{
		$cfg = core::s(\'cfg\');
		$sql = new SQLObject();
		$query = "SELECT text_key,text_value FROM " . $sql->table(\'text\') . " WHERE ((text_lang = \'\' OR text_lang = \'" . $cfg[\'etc\'][\'core\'][\'site_lang\'] . "\') AND (";
		for($i = 0; $i < count($arr[1]); $i++)
		{
			$query .= "text_key = \'" . $arr[1][$i] . "\'";
			if($i != count($arr[1]) - 1) $query .= " OR ";
			else $query .= "))";
		}
		if($sql->query($query))
			foreach($sql->fetch() as $text) self::$output = str_replace(\'{TEXT:\' . $text->text_key . \'}\',$text->text_value,self::$output);
	}',true);
}

if(defined('IN_ACP') && IN_ACP)
{
	Modules::$modules->menu->menu->acp->modules->addItem(
		'./acp.php?c=' . $MODULE_NAME,
		'{L_MODULE_TEXT}',
		array('ACTIVE' => (isset($_GET['c']) && $_GET['c'] == $MODULE_NAME))
	);
}
?>