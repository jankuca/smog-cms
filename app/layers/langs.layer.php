<?php
class Langs
{
	private $dirpath;
	
	public function __construct($dirpath)
	{
		if(!file_exists($dirpath)) return(false);
		$this->dirpath = $dirpath;
		
		$dir = dir($this->dirpath);
		while($file = $dir->read())
		{
			if($file == '.' || $file == '..') continue;
			if(!preg_match('#([a-zA-Z0-9-]+)\.lang\.php#is',$file)) continue;
			
			$this->load(str_replace('.lang.php','',$file));
		}
	}
	
	public function load($filename)
	{
		if(file_exists($this->dirpath . $filename . '.lang.php'))
		{
			include_once($this->dirpath . $filename . '.lang.php');
			core::s('syslog')->success('Langs','__construct()',$this->dirpath . $filename . '.lang.php');
		}
		else core::s('syslog')->error('Langs','__construct()',$this->dirpath . $filename . '.lang.php','File does not exist!');
	}
}
?>