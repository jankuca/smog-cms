<?php
class Template
{
	public $dirpath;
	public $output = '';
	
	private $loadedTpls = array();
	private $assignedVars = array();
	private $assignedLoops = array();
	private $assignedConds = array();
	private $queue = array();
	private $queue2 = array();
	
	public function loadTpl($filename)
	{
		# This method gets contents of the requested .tpl file from $this->dirpath directory (in UTF-8 encoding).
		# @param string $filename
		# @return boolean/string
		 
		global $syslog;
		$path = $this->dirpath . $filename . '.tpl';
		if(!$file = @fopen($path,'r'))
		{
			$syslog->error('Template','loadTpl()',$path,'Cannot load the tpl file.');
			return(false);
		}
		else
		{
			$content = @fread($file,filesize($path));
			$syslog->success('Template','loadTpl()',$path);
			$this->loadedTpls[$filename] = mb_convert_encoding(
				$content,
				'UTF-8',
				mb_detect_encoding((string) $content,array('UTF-8','ISO-8859-1','ISO-8859-2'),true)
			);
			return((string) $this->loadedTpls[$filename]);
		}
	}
	
	public function addTpl($filename)
	{
		# This method adds contents of requested .tpl file to the output.
		# @param string $filename
		# @return boolean
		 
		global $syslog;
		if(!isset($this->loadedTpls[$filename]))
		{
			if(!$this->loadTpl($filename))
			{
				$syslog->error('Template','addTpl()',$this->dirpath . $filename . '.tpl','The tpl file has not been loaded.');
				return(false);
			}
		}
		
		$this->output .= $this->loadedTpls[$filename];
		return(true);
	}
	
	public function display($print = true)
	{
		# This method makes the output and prints it.
		# @param boolean $print
		# [@return string]
		
		global $mod;
		
		foreach($this->queue as $item) eval($item);
		
		for($i = 0; strpos($this->output,'<loop(') != false && $i < 10; $i++) $this->applyLoops();
		$this->applyVars();
		
		foreach($this->queue2 as $item) eval($item);
		
		for($i = 0; strpos($this->output,'<loop(') != false && $i < 10; $i++) $this->applyLoops();
				
		$this->applyConds();
		
		if($print) print($this->output);
		else return($this->output);
	}
	
	public function addQueue($item,$second = false)
	{
		if(!$second) $this->queue[] = $item;
		else  $this->queue2[] = $item;
	}
	
	public function setSiteTitle($title)
	{
		if(defined('IN_ACP') && IN_ACP) $this->assignVar('SITE_TITLE',$title . ' &mdash; {SITE_HEADER} / {L_ACP}');
		else $this->assignVar('SITE_TITLE',$title . ' &mdash; {SITE_HEADER}');
	}
	
	public function assignVar($keys,$value = '')
	{
		# This method assigns values to keys. If the key has already got a value, the value will be overwritten.
		# @param array/string $keys
		# [@param string $value]
		
		if(is_array($keys))
			foreach($keys as $key => $value)
				$this->assignedVars['{' . (string) $key . '}'] = (string) $value;
		else $this->assignedVars['{' . (string) $keys . '}'] = (string) $value;
	}
	
	public function assignCond($keys,$value = false)
	{
		# This method assigns values to keys. If the key has already got a value, the value will be overwritten.
		# @param array/string $keys
		# [@param string $value]
		
		if(is_array($keys))
			foreach($keys as $key => $value)
				$this->assignedConds[(string) $key] = (int) $value;
		else $this->assignedConds[(string) $keys] = (int) $value;
	}
	
	public function assignLoop($key,$items)
	{
		# This method assigns values to keys. If the key has already got a value, the value will be overwritten.
		# @param string $keys
		# @param array $value
		#
		#   The structure of $value HAS TO match this model:
		#		array(
		#			['VARIABLE_KEY' => 'VARIABLE_VALUE',]
		#			['ANOTHER_VARIABLE_KEY' => 'ANOTHER_VARIABLE_VALUE',]
		#			['conds' => array( // list of assigned conditions for the one cycle >> <if(LOOP_KEY.CONDITION_NAME) />
		#				['CONDITION_KEY' => true/false,]
		#				['ANOTHER_CONDITION_KEY' => true/false,]
		#			)]
		#		)
		
		if(!is_array($items)) return(false);
		$this->assignedLoops[(string) $key] = $items;
	}
	
	private function applyVars()
	{
		# This method replaces variable keys with the assigned values.
		
		$this->output = str_replace(array_keys($this->assignedVars),$this->assignedVars,$this->output);
		$this->output = str_replace(array_keys($this->assignedVars),$this->assignedVars,$this->output);
	}
	
	private function applyLoops()
	{
		# This method replaces <loop> tags.
		# The variables (<var />) are replaced with assigned values.
		# The conditions (<if />) are replaced with content responding to the value assigned to the condition key.
		
		// Get all <loop />'s
		preg_match_all('#<loop\(([a-zA-Z0-9_:.-]+)\)>(.*?)</loop\(\\1\)>#is',$this->output,$loops);
		
		foreach($loops[1] as $i => $keyLoop)
		{
			// Validate the assigned loop
			if(!isset($this->assignedLoops[$keyLoop]) || !is_array($this->assignedLoops[$keyLoop]))
				$this->output = str_replace($loops[0][$i],'',$this->output);
			else
			{
				$result = '';	// The final result of the loop
				// Cycle through the assigned variables of the loop
				$o = 0;
				foreach($this->assignedLoops[$keyLoop] as $variables)
				{
					if(!isset($variables['conds'])) $variables['conds'] = array();
					$variables['conds']['FIRST'] = ($o == 0) ? true : false;
					$variables['conds']['LAST'] = ($o == count($this->assignedLoops[$keyLoop]) - 1) ? true : false;
					$row = $loops[2][$i];	// The final result of one cycle
					foreach($variables as $keyVariable => $valueVariable)
					{
						// Conditions:
						if((string) $keyVariable == 'conds')
						{
							// Cycle through the assigned conditions of this cycle
							foreach($valueVariable as $keyCond => $valueCond)
							{
								// Replace in this cycle
								if($valueCond) // true
								{
									$row = preg_replace(
										array(
											'#<if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)<else\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is',
											'#<if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is',
											'#<if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)<else\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is',
											'#<if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is'
										),
										array('$1','$1','$2',''),
										$row
									);
								}
								else	// false
								{
									$row = preg_replace(
										array(
											'#<if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)<else\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is',
											'#<if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is',
											'#<if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)<else\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is',
											'#<if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>(.*?)</if\(!' . ((string) $keyLoop .'.'. $keyCond) . '\)>#is'
										),
										array('$2','','$1','$1'),
										$row
									);
								}
							}
						}
						// Variable: replace in this cycle
						else $row = str_replace('<var(' . ((string) $keyVariable) . ')>',(string) $valueVariable,$row);
					}
					// Add to the final result
					$result .= $row;
					
					$o++;
				}
				// Add the LENGTH "method"
				$result = str_replace('<length('.$keyLoop.')>',count($this->assignedLoops[$keyLoop]),$result);
				// Replace in the output
				$this->output = str_replace($loops[0][$i],$result,$this->output);
			}
		}
	}
	
	private function applyConds()
	{
		# This method replaces all simple conditions with content reponding to the value assigned to the condition key.
		#
		# <if(COND)> content_true [<else(COND)> content_false] </if(COND)>
		# <if(!COND)> content_false [<else(!COND)> content_true] </if(!COND)> (See the "!" sign before the condition key.)
		
		// Step 1 - Positive conds:
		// Get all <if />'s left but those without <else>
		preg_match_all('#<if\(([a-zA-Z0-9_:.-]+)\)>(.*?)<else\(\\1\)>(.*?)</if\(\\1\)>#is',$this->output,$conds);print_r($arr[1]);
		
		foreach($conds[1] as $i => $keyCond)
		{
			// Validate the assigned loop
			if(!isset($this->assignedConds[$keyCond]) || is_array($this->assignedConds[$keyCond]))
				$result = '';
			else
			{
				if($this->assignedConds[$keyCond]) $result = $conds[2][$i];
				else $result = $conds[3][$i];
			}
			// Replace in output
			$this->output = str_replace($conds[0][$i],$result,$this->output);
		}
		
		// Get all <if />'s left
		preg_match_all('#<if\(([a-zA-Z0-9_:.-]+)\)>(.*?)</if\(\\1\)>#is',$this->output,$conds);
		print_r($arr[1]);
		foreach($conds[1] as $i => $keyCond)
		{
			// Validate the assigned loop
			if(!isset($this->assignedConds[$keyCond]) || is_array($this->assignedConds[$keyCond]))
				$result = '';
			else
			{
				if($this->assignedConds[$keyCond]) $result = $conds[2][$i];
				else $result = '';
			}
			// Replace in output
			$this->output = str_replace($conds[0][$i],$result,$this->output);
		}
		
		
		// Step 2 - Negative conds:
		// Get all <if />'s left but those without <else>
		preg_match_all('#<if\(!([a-zA-Z0-9_:.-]+)\)>(.*?)<else\(!\\1\)>(.*?)</if\(!\\1\)>#is',$this->output,$conds);
		
		foreach($conds[1] as $i => $keyCond)
		{
			// Validate the assigned loop
			if(!isset($this->assignedConds[$keyCond]) || is_array($this->assignedConds[$keyCond]))
				$result = $conds[2][$i];
			else
			{
				if($this->assignedConds[$keyCond]) $result = $conds[3][$i];
				else $result = $conds[2][$i];
			}
			// Replace in output
			$this->output = str_replace($conds[0][$i],$result,$this->output);
		}
		
		// Get all <if />'s left
		preg_match_all('#<if\(!([a-zA-Z0-9_:.-]+)\)>(.*?)</if\(!\\1\)>#is',$this->output,$conds);
		
		foreach($conds[1] as $i => $keyCond)
		{
			// Validate the assigned loop
			if(!isset($this->assignedConds[$keyCond]) || is_array($this->assignedConds[$keyCond]))
				$result = $conds[2][$i];
			else
			{
				if($this->assignedConds[$keyCond]) $result = '';
				else $result = $conds[2][$i];
			}
			// Replace in output
			$this->output = str_replace($conds[0][$i],$result,$this->output);
		}
	}
}
?>