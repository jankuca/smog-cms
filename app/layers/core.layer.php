<?php
class core
{
	static public function s($subsystem)
	{
		return($GLOBALS[$subsystem]);
	}
}
?>