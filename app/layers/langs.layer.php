<?php
class Langs
{
	static public $dirpath;
	
	public static function load($filename)
	{
		if(file_exists(self::$dirpath . $filename . '.lang.php'))
		{
			include_once(self::$dirpath . $filename . '.lang.php');
			core::s('syslog')->success('Langs','__construct()',self::$dirpath . $filename . '.lang.php');
		}
		else core::s('syslog')->error('Langs','__construct()',self::$dirpath . $filename . '.lang.php','File does not exist!');
	}
}
?>