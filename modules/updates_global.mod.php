<?php
class module_updates_global
{
	
}

global $mod;
$mod->modules[$MODULE_NAME] = new module_updates_global();

if(defined('IN_UPDATES_GLOBAL') && IN_UPDATES_GLOBAL)
{
	if(isset($_GET['function']))
	{
		switch($_GET['function'])
		{
			case('check_for_updates'):
				if(isset($_POST['XML']))
				{
					include('./app/lib/simplexmlextended.class.php');
					$xml = new SimpleXMLElementExtended(
						html_entity_decode(
							stripslashes($_POST['XML'])
						)
					);
					
					$modules = $xml->xpath('//module');
					
					$count = count($modules);
					if($count > 0)
					{
						$sql = new MySQLObject();
						$query = "
SELECT `code`,`type`,`date`,`name`,`modules`
FROM " . $sql->table('updates_global') . "
WHERE
(";
						$i = 0;
						foreach($modules as $module)
						{
							$query .= "
	`modules` LIKE '%" . $module . "%'";
							if($i < $count - 1)
								$query .= " OR ";
							$i++;
						}
						$query .= "
)
ORDER BY `date` ASC";
						if(!$sql->query($query))
							echo('ERROR');
						else
						{
							if(!$sql->num())
								echo('NO_UPDATES');
							else
							{
								$installed = array();
								foreach($xml->installed->children() as $update)
								{
									$update = $update->getAttributesArray(array('code'));
									$installed[] = $update['code'];
								}
								
								$xml = 
'<?xml version="1.0" encoding="utf-8"?>
<root>' .
	'<updates>';
								$updates = 0;
								foreach($sql->fetch() as $update)
								{
									if(!in_array($update->code,$installed))
									{
										$xml .= 
		'<update type="' . $update->type . '">' .
			'<code>' . $update->code . '</code>' .
			'<name>' . $update->name . '</name>' .
			'<modules>' . $update->modules . '</modules>' .
		'</update>';
										$updates++;
									}
								}
								$xml .= 
	'</updates>' .
'</root>';
								if($updates > 0)
									echo($xml);
								else
									echo('NO_UPDATES');
							}
						}
					}
				}
				break;
			
			case('request_update'):
				if(isset($_POST['XML']))
				{
					include('./app/lib/simplexmlextended.class.php');
					$xml = new SimpleXMLElementExtended(
						html_entity_decode(
							stripslashes($_POST['XML'])
						)
					);
					
					foreach($xml->update->children() as $param)
					{
						$result = $param->getAttributesArray(array('type','value'));
						$update_params[$result['type']] = $result['value'];
					}
					
					$zip = new ZipArchive();
					if(!$zip->open('./data/updates_global/' . $update_params['code'] . '.zip'))
						echo('ERROR');
					else
					{
						if(!file_exists('./data/updates_global/tmp/' . $update_params['code']))
						{
							mkdir('./data/updates_global/tmp/' . $update_params['code']);
							$zip->extractTo('./data/updates_global/tmp/' . $update_params['code']);
							$zip->close();
						}
						
						// -- upload data to FTP --
						foreach($xml->ftp->children() as $param)
						{
							$result = $param->getAttributesArray(array('type','value'));
							$ftp_params[$result['type']] = $result['value'];
						}
						
						include('./app/lib/ftp.class.php');
						global $ftp;
						$ftp = new FTPObject($ftp_params['server'],$ftp_params['username'],$ftp_params['password'],$ftp_params['port'],'/');//$ftp_params[4]);
						$ftp->passive(true);
						
						function upload_contents($dir,$parent,$dirpath)
						{
							global $ftp;
							while($file = $dir->read())
							{
								if($file != '.' && $file != '..')
								{
									if(is_dir($dirpath . $file))
									{
										@$ftp->mkdir($file);
										$ftp->cd($parent . $file);
										$tmp = dir($dirpath . $file);
										upload_contents($tmp,$parent . $file . '/',$dirpath . $file . '/');
										$ftp->cd($parent);
									}
									else
									{
										$ftp->put($file,$dirpath . $file);
									}
								}
							}
						}
						
						$dirpath = './data/updates_global/tmp/' . $update_params['code'] . '/root/';
						$dir = dir($dirpath);
						upload_contents($dir,$ftp->getStart(),$dirpath);
						$ftp->close();
						
						if(!file_exists('./data/updates_global/tmp/' . $update_params['code'] . '/sql.sql'))
							echo('OK');
						else
							echo("SQL\n" . file_get_contents('./data/updates_global/tmp/' . $update_params['code'] . '/sql.sql'));
					}
				}
				break;
		}
	}
}
?>