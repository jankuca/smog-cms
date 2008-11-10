<?php
class TPL
{
	public static $dirpath;
	public static $delimiter_left = '{';
	public static $delimiter_right = '}';
	
	private static $loadedTpls = array();
	private static $assignedVars = array();
	private static $assignedConds = array();
	private static $assignedLoops = array();
	private static $sequence = array();
	private static $modifications = array(array(),array());
	private static $output = '';
	
	public static function loadTpl($name)
	{
		$path = self::$dirpath . $name . '.tpl';
		if(file_exists($path))
		{
			$o = @fopen($path,'r');
			$cnt = @fread($o,filesize($path));
			self::$loadedTpls[(string) $name] = mb_convert_encoding(
				$cnt,
				'UTF-8',
				mb_detect_encoding((string) $cnt,array('UTF-8','ISO-8859-1','ISO-8859-2'),true)
			);
			return(true);
		}
		else return(false);
	}
	
	public static function addTpl($name)
	{
		if(!isset(self::$loadedTpls[(string) $name]))
			if(!self::loadTpl($name)) return(false);
		
		if(isset(self::$loadedTpls[(string) $name]))
			self::$sequence[] = (string) $name;
	}
	
	
	public static function add($key,$value = false)
	{
		if(!is_array($key)) self::$assignedVars[(string) $key] = (string) $value;
		else
			foreach($key as $k => $value) self::$assignedVars[(string) $k] = (string) $value;
	}
	public static function cond($key,$value)
	{
		self::$assignedConds[(string) $key] = (boolean) $value;
	}
	
	public static function assignLoop($loopCodename,&$loop)
	{
		self::$assignedLoops[$loopCodename] = &$loop;
	}
	public static function assignAsLoop($loopCodename,$loop)
	{
		if(is_array($loop))
		{
			$l = new TPLLoop($loopCodename);
			foreach($loop as $loopItem)
			{
				$item = new TPLLoopItem();
				foreach($loopItem as $key => $value)
				{
					if($key != 'conds') $item->add($key,$value);
					else
						foreach($value as $key => $value) $item->cond($key,(boolean) $value);
				}
				$l->append($item);
			}
			$l->pack();
		}
	}
	
	public static function modify($script,$after = false)
	{
		if(!$after) self::$modifications[0][] = $script;
		else self::$modifications[1][] = $script;
	}
	
	public static function pack()
	{
		foreach(self::$sequence as $tpl)
			self::$output .= self::$loadedTpls[$tpl];
		
		foreach(self::$modifications[0] as $mod)
			eval($mod);
		
		self::$output = self::applyConds(self::$output);
		self::$output = self::applyVars(self::$output);
		self::$output = self::applyCounts(self::$output);
		self::$output = self::getAndApplyLoops(self::$output);

		foreach(self::$modifications[1] as $mod)
			eval($mod);
				
		print(self::$output);
	}
	
	
	
	private static function applyConds($content)
	{
		foreach(self::$assignedConds as $key => $value)
		{
			if($value)
			{
				$content = preg_replace(
					array(
						'#<if\(!'.$key.'\)>(.*?)<else\(!'.$key.'\)>(.*?)</if\(!'.$key.'\)>#s',
						'#<if\('.$key.'\)>(.*?)<else\('.$key.'\)>(.*?)</if\('.$key.'\)>#s',
						'#<if\(!'.$key.'\)>(.*?)</if\(!'.$key.'\)>#s',
						'#<if\('.$key.'\)>(.*?)</if\('.$key.'\)>#s'
					),
					array('$2','$1','','$1'),
					$content
				);
			}
			else
			{
				$content = preg_replace(
					array(
						'#<if\(!'.$key.'\)>(.*?)<else\(!'.$key.'\)>(.*?)</if\(!'.$key.'\)>#s',
						'#<if\('.$key.'\)>(.*?)<else\('.$key.'\)>(.*?)</if\('.$key.'\)>#s',
						'#<if\(!'.$key.'\)>(.*?)</if\(!'.$key.'\)>#s',
						'#<if\('.$key.'\)>(.*?)</if\('.$key.'\)>#s'
					),
					array('$1','$2','$1',''),
					$content
				);
			}
		}
		
		return($content);
	}
	
	private static function applyVars($content)
	{
		foreach(self::$assignedVars as $key => $value)
			$vars[self::$delimiter_left . $key . self::$delimiter_right] = $value;
		
		return(str_replace(array_keys($vars),$vars,$content));
	}
	
	private static function applyCounts($content)
	{
		preg_match_all('#<count\(([a-zA-Z0-9_:.-]+)\)>#s',$content,$arr);
		foreach($arr[1] as $i => $loopKey)
		{
			if(isset(self::$assignedLoops[$loopKey]))
				$content = str_replace($arr[0],self::$assignedLoops[$loopKey]->count,$content);
			else
				$content = str_replace($arr[0],'0',$content);
		}
		
		return($content);
	}
	
	private static function getAndApplyLoops($content)
	{
		if(mb_strpos($content,'<loop(',0,'UTF-8') !== false)
		{
			preg_match_all('#<loop\(([a-zA-Z0-9_:.-]+)\)>(.*?)</loop\(\\1\)>#s',$content,$arr);
			foreach($arr[2] as $i => $cnt)
			{
				$cnt = self::getAndApplyLoops($cnt);
				
				$result = '';
				for($p = 0; $p < count(self::$assignedLoops[$arr[1][$i]]->items); ++$p)
				{
					$patt = $cnt;
					preg_match_all('#<var\(([a-zA-Z0-9_.-]+)\)>#s',$patt,$arr2);
					foreach($arr2[1] as $o => $varKey)
						if(isset(self::$assignedLoops[$arr[1][$i]]->items[$p]->vars[$varKey]))
							$patt = str_replace($arr2[0][$o],self::$assignedLoops[$arr[1][$i]]->items[$p]->vars[$varKey],$patt);
					
					preg_match_all('#<var\(([a-zA-Z0-9_.-]+)\)raw>#s',$patt,$arr2);
					foreach($arr2[1] as $o => $varKey)
						if(isset(self::$assignedLoops[$arr[1][$i]]->items[$p]->vars[$varKey]))
							$patt = str_replace($arr2[0][$o],self::applyVars(self::$assignedLoops[$arr[1][$i]]->items[$p]->vars[$varKey]),$patt);
					
					if(count(self::$assignedLoops[$arr[1][$i]]->items[$p]->conds) > 0)
					{
						foreach(self::$assignedLoops[$arr[1][$i]]->items[$p]->conds as $condKey => $value)
						{
							if(self::$assignedLoops[$arr[1][$i]]->items[$p]->conds[$condKey])
								$patt = preg_replace(
									array(
										'#<if\(!'.$arr[1][$i].'.'.$condKey.'\)>(.*?)<else\(!'.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\(!'.$arr[1][$i].'.'.$condKey.'\)>#s',
										'#<if\('.$arr[1][$i].'.'.$condKey.'\)>(.*?)<else\('.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\('.$arr[1][$i].'.'.$condKey.'\)>#s',
										'#<if\(!'.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\(!'.$arr[1][$i].'.'.$condKey.'\)>#s',
										'#<if\('.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\('.$arr[1][$i].'.'.$condKey.'\)>#s'
									),
									array('$2','$1','','$1'),
									$patt
								);
							else
								$patt = preg_replace(
									array(
										'#<if\(!'.$arr[1][$i].'.'.$condKey.'\)>(.*?)<else\(!'.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\(!'.$arr[1][$i].'.'.$condKey.'\)>#s',
										'#<if\('.$arr[1][$i].'.'.$condKey.'\)>(.*?)<else\('.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\('.$arr[1][$i].'.'.$condKey.'\)>#s',
										'#<if\(!'.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\(!'.$arr[1][$i].'.'.$condKey.'\)>#s',
										'#<if\('.$arr[1][$i].'.'.$condKey.'\)>(.*?)</if\('.$arr[1][$i].'.'.$condKey.'\)>#s'
									),
									array('$1','$2','$1',''),
									$patt
								);
						}
					}
					$result .= $patt;
				}
				
				$content = str_replace($arr[0][$i],$result,$content);
			}
		}
		return($content);
	}
}

class TPLLoop
{
	private $loopCodename;
	public $count;
	public $items;
	
	public function __construct($loopCodename)
	{
		$this->loopCodename = $loopCodename;
		$this->count = 0;
		$this->items = array();
	}
	
	public function append($item)
	{
		$this->items[] = $item;
		++$this->count;
	}
	
	public function pack()
	{
		TPL::assignLoop($this->loopCodename,$this);
	}
}

class TPLLoopItem
{
	public $vars;
	public $conds;
	
	public function __construct()
	{
		$this->vars = array();
		$this->conds = array();
	}
	public function add($key,$value)
	{
		$this->vars[(string) $key] = $value;
	}
	public function cond($key,$value)
	{
		$this->conds[(string) $key] = (boolean) $value;
	}
}
?>