<?php
TPL::add(array(
	'L_MODULE_AUTH' => 'Autorizace',
	'L_MODULE_AUTH_DESCRIPTION' => 'Správa uživatelů a nastavení oprávnění',
	
	'L_LOGIN' => 'Přihlásit se',
	'L_LOGOUT' => 'Odhlásit se',
	
	'L_AUTH_ALERT_TOO_LITTLE_DATA' => 'Příliš málo dat!',
	
	'L_AUTH_LOGGED_AS' => 'Jste přihlášen jako {CURRENT_USER.USERNAME}.',
	'L_AUTH_ALERT_LOGIN_INACTIVE_USER' => 'Váš účet ještě nebyl aktivován!',
	'L_AUTH_ALERT_LOGIN_INVALID_PASSWORD' => 'Špatné heslo. <a href="{HTTP_REFERER}">Zkusit znovu</a>',
	'L_AUTH_ALERT_INVALID_USERNAME' => 'Neplatné uživatelské jméno:<ul><li>Rozsah 3-32 znaků</li><li>Může obsahovat pouze a-z, A-Z, 0-9, _, - a mezeru.</li></ul>',
	'L_AUTH_ALERT_INVALID_PASSWORD' => 'Neplatné heslo:<ul><li>Rozsah 8-24 znaků</li><li>Může obsahovat a-z, A-Z, 0-9 a následující: !.:@$#%^*&()~"\\\'\\/[]</li></ul>',
	'L_AUTH_ALERT_ADD_USER_ERROR' => 'Uživatel nebyl přidán.',
	'L_AUTH_ALERT_USER_ADDED' => 'Uživatel byl úspěšně přidán.',
	'L_AUTH_ALERT_TOO_LITTLE_DATA' => 'Bylo odesláno příliš málo dat.',
	
	'L_AUTH_ALERT_INVALID_GROUPNAME' => 'Neplatné jméno skupiny:<ul><li>Rozsah 1-128 znaků</li></ul>',
	'L_AUTH_ALERT_GROUP_ADDED_PERMISSIONS' => 'Skupina byla úspěšně přidána a byla jí přidělena požadovaná oprávnění.',
	'L_AUTH_ALERT_GROUP_ADDED_SOME_PERMISSIONS' => 'Skupina byla úspěšně přidána, ale byla jí přidělena pouze některá požadované oprávnění.',
	'L_AUTH_ALERT_GROUP_ADDED_NO_PERMISSIONS' => 'Skupina byla úspěšně přidána, ale nebyla jí přidělena požadovaná oprávnění.',
	
	'L_AUTH_ALERT_GROUP_DELETED' => 'Skupina byla úspěšně smazána.',
	'L_AUTH_ALERT_GROUP_ONLY_PERMISSIONS_DELETED' => 'Skupina nebyla smazána, ale byla jí odstraněna veškerá oprávnění.',
	'L_AUTH_ALERT_DELETE_GROUP_ERROR' => 'Skupina nebyla odstraněna.',
	
	'L_AUTH_ALERT_USER_DELETED' => 'Uživatel byl úspěšně smazán.',
	'L_AUTH_ALERT_DELETE_USER_ERROR' => 'Uživatel nebyl smazán.',
	
	'L_AUTH_ALERT_NO_CHALLENGE' => 'Nemáte platný ticket pro přihlášení.</p><p>Pro obnovení ticketu musíte aktualizovat stránku s přihlašovaním dialogem.</p><p><a href="{HTTP_REFERER}">Zpět na předchozí stránku</a>',
	
	'L_PERMISSION_AUTH_ACP_ENTER' => 'Nemáte oprávnění vstupovat do administračního rozhraní!',
	'L_PERMISSION_AUTH_ADD_USER' => 'Namáte oprávnění přidávat uživatele!',
	'L_PERMISSION_AUTH_DELETE_USER' => 'Nemáte oprávnění mazat uživatele.',
	'L_PERMISSION_AUTH_ADD_GROUP' => 'Namáte oprávnění přidávat skupiny!',
	'L_PERMISSION_AUTH_DELETE_GROUP' => 'Nemáte oprávnění mazat skupiny!',
	
	'L_AUTHBOX_TITLE' => 'Autorizace',
	'L_USERNAME' => 'Uživatelské jméno',
	'L_PASSWORD' => 'Heslo',
	'L_AUTH_SAVE' => 'Zapamatovat',
	
	'L_USERS' => 'Uživatelé',
	'L_USERS_USERS' => 'Uživatelé',
	'L_USERS_GROUPS' => 'Skupiny',
	'L_USERS_GROUPS_INFO' => 'Od příslušnosti ve skupinách se odvíjejí uživatelova oprávnění.',
	
	'L_USERS_ADD_USER' => 'Přidat uživatele',
	'L_USERS_ADD_USER_SUBMIT' => 'Přidat uživatele',
	'L_USERS_ADD_GROUP' => 'Přidat skupinu',
	'L_USERS_ADD_GROUP_SUBMIT' => 'Přidat skupinu',
	
	'L_ONLINE_USERS' => 'Uživatelé online',
	'L_ONLINE_USERS_NO_USERS' => 'Žádný uživatel není online.',
	'L_ONLINE_ADMINS' => 'Administrátoři online',
	'L_ONLINE_ADMINS_NO_ADMINS' => 'Žádný administrátor není online.',
	
	'L_USERS_COUNT_USERS' => 'Počet uživatelů',
	'L_USERS_COUNT_GROUPS' => 'Počet skupin',
	
	
	'L_USERNAME_INFO' => 'Rozsah 3-32 znaků; Může obsahovat pouze a-z, A-Z, 0-9, _, - a mezeru.',
	'L_USERS_EMAIL' => 'E-mail',
	'L_USERS_EMAIL_ERROR' => 'Zadaná hodnota není platná e-mailová adresa.',
	'L_USERS_GENDER' => 'Pohlaví',
	'L_GENDER_MALE' => 'Muž',
	'L_GENDER_FEMALE' => 'Žena',
	'L_GENDER_NA' => 'Neuvedeno',
	'L_PASSWORD_INFO' => 'Rozsah 8-24 znaků; Může obsahovat a-z, A-Z, 0-9 a následující: !.:@$#%^*&()~"\'/[]\\',
	'L_PASSWORD_STRENGTH_INVALID' => 'Neplatné heslo: Rozsah 8-24 znaků; Může obsahovat a-z, A-Z, 0-9 a následující: !.:@$#%^*&()~"\\\'\\/[]',
	'L_PASSWORD_STRENGTH_WEAK' => 'Slabé heslo',
	'L_PASSWORD_STRENGTH_NORMAL' => 'Normální heslo',
	'L_PASSWORD_STRENGTH_STRONG' => 'Silné heslo',
	'L_PASSWORD_CONFIRM' => 'Ověření hesla',
	'L_PASSWORD_CONFIRM_ERROR' => 'Hesla se neshodují',
	
	'L_USER_GROUPS_INFO' => 'Každý uživatel je automaticky zařazen do skupiny <strong>Uživatelé</strong>.',
	'L_USER_GROUPS_MULTIPLE_INFO' => 'V případě příslušnosti do více skupin se vždy načtou veškerá oprávnění všech skupin.',
	'L_USER_GROUP_MAIN' => 'Zaškrtávací políčko určuje <strong>příslušnost</strong> do skupin a přepínač <strong>zobrazovanou</strong> skupinu v uživatelově profilu.',
	
	
	'L_GROUP_NAME' => 'Název skupiny',
	'L_GROUP_NAME_INFO' => 'Rozsah 1-128 znaků',
	'L_GROUP_DESCRIPTION' => 'Popis skupiny',
	'L_GROUP_DESCRIPTION_INFO' => 'Maximálně 256 znaků; Nepovinné',
	'L_GROUP_PERMISSIONS' => 'Oprávnění skupiny',
	'L_GROUP_PERMISSIONS_INFO' => 'Oprávnění přidělená všem členům skupiny',
	
	
	'L_PERMISSION.auth.acp' => 'Administrace',
	'L_PERMISSION.auth.acp.enter' => 'Povolit vstup',
	'L_PERMISSION.auth.user' => 'Uživatelé',
	'L_PERMISSION.auth.user.add' => 'Přidávat',
	'L_PERMISSION.auth.user.edit' => 'Upravovat',
	'L_PERMISSION.auth.user.delete' => 'Mazat',
	'L_PERMISSION.auth.user.change_groups' => 'Měnit příslušnost ve skupinách (oprávnění)',
	'L_PERMISSION.auth.group' => 'Skupiny',
	'L_PERMISSION.auth.group.add' => 'Přidávat',
	'L_PERMISSION.auth.group.edit' => 'Upravovat (oprávnění)',
	'L_PERMISSION.auth.group.delete' => 'Mazat'
));
?>