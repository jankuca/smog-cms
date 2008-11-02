<?php
$sql = new SQLObject();

$query = "DELETE FROM " . $sql->table('modules') . "
WHERE (filename = '" . $_GET['module_codename'] . ".mod.php')";

if($sql->exec($query)) echo('  [<strong>x</strong>] sql> ' . str_replace("\r\n",' ',$query) . "\n");
else echo('  [ ] sql> ' . str_replace("\r\n",' ',$query) . "\n");


$query = "DROP TABLE " . $sql->table('updates_global');
if($sql->exec($query)) echo('  [<strong>x</strong>] sql> ' . str_replace("\r\n",' ',$query) . "\n");
else echo('  [ ] sql> ' . str_replace("\r\n",' ',$query) . "\n");

?>