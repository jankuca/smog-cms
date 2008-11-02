<?php
$sql = new SQLObject();
$history[] = '$sql = new SQLObject();';


$query = "INSERT INTO " . $sql->table('modules') . "
(core,seq,active,filename,name,description,author,version)
VALUES (
	0,
	(SELECT MAX(seq)+1 FROM " . $sql->table('modules') . " WHERE (core = 0)),
	0,
	'" . $_GET['module_codename'] . ".mod.php',
	'{L_MODULE_UPDATES_GLOBAL}',
	'{L_MODULE_UPDATES_GLOBAL_DESCRIPTION}',
	'" . $info['author'] . "',
	'" . $info['version'] . "'
)";

if($sql->exec($query)) echo('  [<strong>x</strong>] sql> ' . str_replace("\r\n",' ',$query) . "\n");
else die('  [ ] sql> ' . str_replace("\r\n",' ',$query) . "\n");
$history[] = '$sql->exec("DELETE FROM " . $sql->table(\'modules\') . " WHERE (filename = \'' . $_GET['module_codename'] . '\')");';


$query = "CREATE TABLE " . $sql->table('updates_global') . "
(
	update_id INTEGER PRIMARY KEY,
	update_code VARCHAR(19) UNIQUE,
	update_type VARCHAR(2),
	update_date VARCHAR(16),
	update_name VARCHAR(64),
	update_modules VARCHAR(256),
	update_pack VARCHAR(256),
	update_downloaded INTEGER DEFAULT '0'
)";

if($sql->exec($query)) echo('  [<strong>x</strong>] sql> ' . str_replace("\r\n",' ',$query) . "\n");
else { echo('  [ ] sql> ' . str_replace("\r\n",' ',$query) . "\n"); undo_installation($history); }
$history[] = '$sql->exec("DROP TABLE IF EXISTS " . $sql->table(\'modules\'));';

?>