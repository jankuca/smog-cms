<?php
class SysLog
{
	private $log = array();
	public $start = 0;
	
	public function error($layer,$method,$item,$message,$show = true)
	{
		$this->log[] = array(
			'type' => 'Error',
			'layer' => $layer,
			'method' => $method,
			'item' => $item,
			'message' => $message,
			'time' => microtime(true) - $this->start
		);
		
		global $cfg;
		if($show && (isset($cfg['etc']['core']['show_errors']) && $cfg['etc']['core']['show_errors']))
		{
			echo
				'<div style="margin: 0 0 5px; padding: 0 10px; border-left: 5px solid #F02; background: #111; color: #EEE; font-family: monospace; font-size: 12px; text-align: left;">',
				'<strong>Layer:</strong> ',$layer,'<br />',
				'<strong>Method:</strong> ',$method,'<br />',
				'<strong>Item:</strong> ',$item,'<br />',
				'<strong>Message:</strong> ',$message,'<br />',
				'</div>'
			;
		}
	}
	
	public function success($layer,$method,$item,$show = false)
	{
		$this->log[] = array(
			'type' => 'Success',
			'layer' => $layer,
			'method' => $method,
			'item' => $item,
			'time' => microtime(true) - $this->start
		);
		
		global $cfg;
		if($show && (isset($cfg['etc']['core']['show_errors']) && $cfg['etc']['core']['show_errors']))
		{
			echo
				'<div style="margin: 0 0 5px; padding: 0 10px; border-left: 5px solid #0C0; background: #111; color: #EEE; font-family: monospace; font-size: 12px; text-align: left;">',
				'<strong>Layer:</strong> ',$layer,'<br />',
				'<strong>Method:</strong> ',$method,'<br />',
				'<strong>Item:</strong> ',$item,'<br />',
				'</div>'
			;
		}
	}
	
	public function log()
	{
		echo
		'<table id="syslog" cellspacing="0" style="border: 1px solid #CCC; background: #FFF; font-size: 11px;">',
		'	<thead>',
		'		<tr>',
		'			<th style="width: 50px">Time</th>',
		'			<th style="width: 100px">Type</th>',
		'			<th style="width: 150px">Layer</th>',
		'			<th style="width: 150px">Method</th>',
		'			<th>&nbsp;</th>',
		'		</tr>',
		'	</thead>';
		
		$last_time = 0;
		foreach($this->log as $item):
		echo
		'	<tbody>',
		'		<tr>',
		'			<td>' . ((int) (round($item['time'],3) * 1000) - $last_time) . ' ms</td>',
		'			<td>' . $item['type'] . '</td>',
		'			<td>' . $item['layer'] . '</td>',
		'			<td>' . $item['method'] . '</td>',
		'			<td>',
		$item['item'];
		if($item->type == 'Error') echo $item['message'];
		echo
		'			</td>',
		'		</tr>',
		'	</tbody>';
		$last_time = round($item['time'],3) * 1000;
		endforeach;
		echo
		'</table>';
	}
}
$syslog = new SysLog();
?>