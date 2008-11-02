<?php
// The first load of this file
if(!defined('REDIRECTED'))
{
	require_once('./app/layers/syslog.layer.php');
	
	ini_set('precision','18');
	$syslog->start = microtime(true);;
	
	$dir = dir('./app/lib/');
	while($file = $dir->read())
	{
		if($file == '.' || $file == '..') continue;
		if(!preg_match('#([a-zA-Z0-9-]+)\.(lib|class)\.php#is',$file)) continue;
		
		ob_start();
		if(include_once('./app/lib/' . $file)) $syslog->success('(root)','include_once()','./app/lib/' . $file);
		else $syslog->success('(root)','include_once()','./app/lib/' . $file,ob_get_contents());
		ob_end_clean();
	}
	unset($dir,$file);
	
	rm_magicquotes();
	
	require_once('./app/layers/sqlite.layer.php');
	
	// Init the connection to the database
	$sqlite = new SQLite('./data/sql/.database.sqlite','scms_',0777);
	
	
	if(defined('REQUEST'))
	{
		$sql = new SQLObject();
		if($sql->query("
SELECT request_id,request_regexp,request_uri,request_target
FROM " . $sql->table('requests') . "
ORDER BY request_order ASC"))
		{
			foreach($sql->fetch() as $request)
			{
				if((boolean) $request->request_regexp === false)
				{
					if(REQUEST == $request->request_uri || REQUEST . '/' == $request->request_uri)
						define('LOC',$request->request_target);
				}
				elseif(preg_match($request->request_uri,REQUEST) || preg_match($request->request_uri,REQUEST . '/'))
					define('LOC',$request->request_target);
			}
		}
		unset($sql);
		
		if(!defined('LOC')) { header('Location: ../index.php'); }
		else
		{
			define('REDIRECTED',true);
			include(LOC);
		}
	}
	else require_once('./init.php');
}

// The second load of this file
else require_once('./init.php');
?>