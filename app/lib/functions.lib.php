<?php
function rm_magicquotes()
{
	if(get_magic_quotes_gpc())
	{
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_FILES);
		while(list($key, $val) = each($process))
		{
			foreach ($val as $k => $v)
			{
				unset($process[$key][$k]);
				if (is_array($v))
				{
					$process[$key][($key < 5 ? $k : stripslashes($k))] = $v;
					$process[] =& $process[$key][($key < 5 ? $k : stripslashes($k))];
				}
				else
				{
					$process[$key][stripslashes($k)] = stripslashes($v);
   			}
			}
		}
	}
}

function formatDateTime($unix,$type = 'datetime')
{
	global $cfg;
	switch($type)
	{
		case('datetime'): return(date($cfg['etc']['core']['datetime_format'],(int) $unix)); break;
		case('date'): return(date($cfg['etc']['core']['date_format'],(int) $unix)); break;
		default: return(date($cfg['etc']['core']['datetime_format'],(int) $unix)); break;
	}
}

function array_remove_empty($array,$keep_keys = true)
{
	# Removes the empty values from (array) $array.
	# If $keep_keys is false, the function returns array with numeric keys starting with 0.
	
	$out = array();
	foreach($array as $key => $value)
	{
		if($value != '' && $value != NULL)
		{
			if($keep_keys)
				$out[$key] = $value;
			else
				$out[] = $value;
		}
	}
	return($out);
}
?>