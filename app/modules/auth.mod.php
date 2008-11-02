<?php
/*
$sql = new SQLObject();
$sql->exec("INSERT INTO scms_auth_groups VALUES ('-1', 'Anonymní', 'Všichni neregistrovaní návštěvníci');");
*/
function permission($module,$name,$value)
{
	if(isset($_SESSION['permissions'][$module][$name]) && in_array($value,$_SESSION['permissions'][$module][$name]))
		return(true);
	else
		return(false);
}

class module_auth
{
	public function __construct()
	{
		$this->_module_info['path'] = './app/modules/' . basename(__FILE__);
		
		$sql = new SQLObject();
		session_start();
		if(!isset($_SESSION['logged']))
		{
			if(isset($_COOKIE['AUTHKEY']))
			{
				if($sql->query("SELECT user_id AS id,user_username AS username,user_password_hash AS password_hash,user_email AS email,user_groups AS groups FROM " . $sql->table('auth_users') . " WHERE (AUTHKEY = '" . $_COOKIE['AUTHKEY'] . "')") && $sql->num_rows())
				{
					$user = $sql->fetch_one();
					$_SESSION['logged'] = true;
					$_SESSION['user']['id'] = (int) $user->id;
					$_SESSION['user']['username'] = (string) $user->username;
					$_SESSION['user']['email'] = (string) $user->email;
					$_SESSION['groups'] = array_remove_empty(explode(';',$user->groups));
					$this->setLoginTime();
				}
				else $this->logout();
			}
			else $this->logout();
		}
		
		if($_SESSION['logged'])
		{
			core::s('tpl')->assignVar(array(
				'CURRENT_USER.ID' => $_SESSION['user']['id'],
				'CURRENT_USER.USERNAME' => $_SESSION['user']['username'],
				'CURRENT_USER.EMAIL' => $_SESSION['user']['email']
			));
			core::s('tpl')->assignCond('LOGGED',true);
			
			if($sql->query("SELECT user_groups FROM " . $sql->table('auth_users') . " WHERE (user_id = " . $_SESSION['user']['id']. ")") && $sql->num_rows())
			{
				$user = $sql->fetch_one();
				$_SESSION['groups'] = array_remove_empty(explode(';',$user->user_groups));
			}
		}
		else core::s('tpl')->assignCond('LOGGED',false);
		
		$this->setAuthkey();
		$this->saveSessionTime();
		$this->loadPermissions();
	}
	
	public function getChallenge()
	{
		$salt = rand(100,999);
		$cookie = rand(100,999);
		
		$sql = new SQLObject();
		if($sql->exec("
INSERT INTO " . $sql->table('auth_challenges') . "
(challenge_id,challenge_ip,challenge_salt,challenge_cookie)
VALUES
(NULL,'" . $_SERVER['REMOTE_ADDR'] . "'," . $salt . "," . $cookie . ")"))
		{
			setcookie('AUTH_COOKIE',$cookie);
			
			core::s('tpl')->assignVar('AUTH_CHALLENGE',$sql->last_insert_id());
			core::s('tpl')->assignVar('AUTH_SALT',$salt);
		}
	}
	
	public function setAuthkey()
	{
		if($_SESSION['logged'])
		{
			$authkey = md5(time() . $_SERVER['REMOTE_ADDR']);
			$_SESSION['AUTHKEY'] = $authkey;
			setcookie('AUTHKEY',$authkey);
			$sql = new SQLObject();
			$sql->exec("
UPDATE " . $sql->table('auth_users') . "
SET
	AUTHKEY = '" . $authkey . "',
	session_last_time = " . $this->saveSessionTime() . "
WHERE (user_id = " . $_SESSION['user']['id'] . ")");
		}
	}
	
	public function logout()
	{
		$sql = new SQLObject();
		if($_SESSION['logged'])
			$sql->exec("UPDATE " . $sql->table('auth_users') . " SET session_last_time = 0 WHERE (user_id = " . $_SESSION['user']['id'] . ")");
		
		session_destroy();
		setcookie('AUTHKEY','');
		session_start();
		$_SESSION = array();
		$_SESSION['logged'] = false;
		$_SESSION['user']['id'] = -1;
		$_SESSION['groups'] = array(-1);
		
		$this->loadPermissions();
	}
	
	private function loadPermissions()
	{
		$_SESSION['permissions'] = array();
		
		$sql = new SQLObject();
		$query = "SELECT module,name,value FROM " . $sql->table('auth_permissions') . " WHERE (user_id = " . $_SESSION['user']['id'];
		foreach($_SESSION['groups'] as $group) $query .= " OR group_id = " . $group;
		$query .= ")";
		if($sql->query($query))
		{
			foreach($sql->fetch() as $item)
			{
				if(!isset($_SESSION['permissions'][$item->module][$item->name])) $_SESSION['permissions'][$item->module][$item->name] = array();
				$perm = explode(';',$item->value);
				foreach($perm as $p)
				{
					if(!in_array($p,$_SESSION['permissions'][$item->module][$item->name]))
					{
						$_SESSION['permissions'][$item->module][$item->name][] = $p;
						core::s('tpl')->assignCond('AUTH.PERMISSION.' . strtoupper($item->module) . '.' . strtoupper($item->name) . '.' . strtoupper($p),true);
					}
				}
			}
		}
	}
	
	private function saveSessionTime()
	{
		$session_last_time = time();
		$_SESSION['session_last_time'] = $session_last_time;
		return($session_last_time);
	}
	
	public function getOnlineUsers($count = 0,$groups = array(),$loop_key = 'ONLINE_USERS')
	{
		$sql = new SQLObject();
		$cfg = core::s('cfg');
		$query = "
SELECT
	" . $sql->table('auth_users') . ".user_id AS user_id,
	" . $sql->table('auth_users') . ".user_username AS user_username,
	" . $sql->table('auth_users') . ".user_group_main AS user_group_main,
	" . $sql->table('auth_users') . ".session_last_time AS session_last_time,
	" . $sql->table('auth_groups') . ".group_name AS group_name
FROM " . $sql->table('auth_users') . "
LEFT JOIN " . $sql->table('auth_groups') . "
	ON " . $sql->table('auth_users') . ".user_group_main = " . $sql->table('auth_groups') . ".group_id
WHERE (session_last_time >= " . (time() - $cfg['etc']['core']['online_offset']);

		if($groups)
		{
			$query .= " AND (";
			$i = 0;
			foreach($groups as $group_id)
			{
				$query .= " user_groups LIKE '%" . $group_id . "%'";
				if($i != count($groups) - 1) $query .= " AND";
				else $query .= ")";
				$i++;
			}
		}
		$query .= ") ORDER BY session_last_time DESC";
		if($count) $query .= " LIMIT 0," . (int) $count;
		
		if(!$sql->query($query))
		{
			core::s('tpl')->assignCond($loop_key,false);
			core::s('tpl')->assignLoop($loop_key,array());
			echo($sql->error);
		}
		else
		{
			core::s('tpl')->assignCond($loop_key,true);
			$f_users = array();
			foreach($sql->fetch() as $user)
				$f_users[] = array(
					'USER_ID' => $user->user_id,
					'USER_USERNAME' => $user->user_username,
					'USER_GROUP' => $user->user_group_main,
					'USER_SESSION_TIME_OFFSET' => $this->getSessionTimeOffset($user->session_last_time),
					'conds' => array(
						'ADMIN' => (in_array(1,(array) $groups) || (int) $user->user_group_main == 1)
					)
				);
			core::s('tpl')->assignLoop($loop_key,$f_users);
		}
	}
	
	public function getGroupsList($user_id = 0,$only_user = 0,$exceptions = 0,$anonymous = 0)
	{
		if(!$exceptions) $exceptions = array();
		
		$sql = new SQLObject();
		if(!$user_id) $query = "SELECT group_id,group_name,group_description FROM " . $sql->table('auth_groups') . (!$anonymous ? " WHERE (group_id != -1)" : "") . " ORDER BY group_name ASC";
		//else
		//{
			//if($only_user == false) $query = "SELECT group_id,group_name FROM " . $sql->table('auth_groups') . " AS groups  ORDER BY group_name ASC";
		
		if($sql->query($query))
		{
			$f_groups = array();
			foreach($sql->fetch() as $group)
			{
				$f_groups[] = array(
					'GROUP_ID' => $group->group_id,
					'GROUP_NAME' => $group->group_name,
					'GROUP_DESCRIPTION' => $group->group_description,
					'conds' => array(
						'EXCEPTION' => (in_array($group->group_id,$exceptions) ? true : false)
					)
				);
			}
			
			core::s('tpl')->assignLoop('USER_GROUPS',$f_groups);
		}
	}
	
	public function getSessionTimeOffset($time_last,$round = 0,$time_now = false)
	{
		if(!$time_now) $time_now = time();
		return(round(($time_now - (int) $time_last) / 60,$round));
	}
	
	public function setLoginTime()
	{
		$sql = new SQLObject();
		if($sql->exec("UPDATE " . $sql->table('auth_users') . " SET login_last_time = " . time() . " WHERE (user_id = " . $_SESSION['user']['id'] . ")")) return(true);
		else return(false);
	}
	
	public function users_getCounts()
	{
		
		$sql = new SQLObject();
		if($sql->query("SELECT COUNT(*) AS count_users FROM " . $sql->table('auth_users')))
			core::s('tpl')->assignVar('USERS_COUNT_USERS',$sql->fetch_one()->count_users);
		else
			core::s('tpl')->assignVar('USERS_COUNT_USERS','N/A');
		
		if($sql->query("SELECT COUNT(*) AS count_groups FROM " . $sql->table('auth_groups')))
			core::s('tpl')->assignVar('USERS_COUNT_GROUPS',$sql->fetch_one()->count_groups);
		else
			core::s('tpl')->assignVar('USERS_COUNT_GROUPS','N/A');
	}
	
	public function users_getUserlist($order = 'user_username ASC')
	{
		$sql = new SQLObject();
		$p = new Pages();
		$p->url = './acp.php?c=users&amp;section=users&amp;page=%page';
		$p->per_page = 1;
		$p->query = "
SELECT u.user_id,u.user_username,u.user_gender,u.user_group_main,g.group_name
FROM " . $sql->table('auth_users') . " AS u
LEFT JOIN " . $sql->table('auth_groups') . " AS g
	ON u.user_group_main = g.group_id
ORDER BY " . $order;

		$f_users = array();
		if($p->make())
		{
			foreach($p->fetch() as $user)
			{
				$f_users[] = array(
					'USER_ID' => $user->{'u.user_id'},
					'USER_USERNAME' => $user->{'u.user_username'},
					'USER_GROUP_ID' => $user->{'u.user_group_main'},
					'USER_GROUP_NAME' => $user->{'g.group_name'},
					'USER_ACTION_EDIT' => './acp.php?c=users&amp;section=users&amp;mode=edit&amp;user_id=' . $user->{'u.user_id'},
					'USER_ACTION_DELETE' => './action.php?c=users&amp;section=users&amp;mode=delete&amp;user_id=' . $user->{'u.user_id'},
					'conds' => array(
						'USER_GENDER_MALE' => ((int) $user->{'u.user_gender'} == 1) ? true : false,
						'USER_GENDER_FEMALE' => ((int) $user->{'u.user_gender'} == 0) ? true : false,
						'USER_GENDER_NA' => ((int) $user->{'u.user_gender'} == 2) ? true : false
					)
				);
			}
		}
		core::s('tpl')->assignLoop('AUTH_USERLIST',$f_users);
		core::s('tpl')->assignVar('PAGES_USERLIST',$p->browser());
	}
	
	public function groups_getGrouplist()
	{
		$sql = new SQLObject();
		$p = new Pages();
		$p->url = './acp.php?c=users&amp;section=groups&amp;page=%page';
		$p->per_page = 25;
		$p->query = "SELECT group_id,group_name,group_description FROM " . $sql->table('auth_groups') . " ORDER BY group_name ASC";
		
		$f_groups = array();
		if($p->make())
			foreach($p->fetch() as $group)
			{
				$f_groups[] = array(
					'GROUP_ID' => $group->group_id,
					'GROUP_NAME' => $group->group_name,
					'GROUP_DESCRIPTION' => $group->group_description,
					'GROUP_ACTION_EDIT' => './acp.php?c=users&amp;section=groups&amp;mode=edit',
					'GROUP_ACTION_DELETE' => './action.php?c=users&amp;section=groups&amp;mode=delete&amp;group_id=' . $group->group_id,
					'conds' => array(
						'ANONYMOUS' => ((int) $group->group_id == -1) ? true : false
					)
				);
			}
		
		core::s('tpl')->assignLoop('AUTH_GROUPLIST',$f_groups);
		core::s('tpl')->assignVar('PAGES_GROUPLIST',$p->browser());
	}
	
	public function getPermissionsAvailable($empty = false)
	{
		$sql = new SQLObject();
		if($sql->query("
SELECT p.permission_module_codename AS module_codename, p.permission_name AS codename, p.permission_value AS value, m.name AS module_name
FROM " . $sql->table('modules_permissions') . " AS p
LEFT JOIN " . $sql->table('modules') . " AS m
ON p.permission_module_codename||'.mod.php' = m.filename"))
		{
			$f_permissions_modules = array();
			$f_permissions = array();
			$modules = array();
			foreach($sql->fetch() as $perm)
			{
				if(!in_array($perm->module_name,$modules))
				{
					$f_permissions_modules[] = array(
						'PERMISSIONS_MODULE' => $perm->module_name,
						'PERMISSIONS_MODULE_CODENAME' => $perm->module_codename,
					);
					$modules[] = $perm->module_name;
				}
				
				$f_permissions[$perm->module_codename][] = array(
					'PERMISSIONS_CODENAME' => $perm->codename,
					'PERMISSIONS_NAME' => '{L_PERMISSION.' . $perm->module_codename . '.' . $perm->codename . '}'
				);
				
				$values = explode(';',$perm->value);
				$f_values = array();
				$i = 0;
				foreach($values as $value)
				{
					$f_values[] = array(
						'PERMISSION_NAME' => '{L_PERMISSION.' . $perm->module_codename . '.' . $perm->codename . '.' . $value . '}',
						'PERMISSION_VALUE' => $value
					);
					$i++;
				}
				
				core::s('tpl')->assignLoop('GROUP_PERMISSIONS-' . $perm->module_codename . '-' . $perm->codename,$f_values);
			}
			
			core::s('tpl')->assignLoop('GROUP_PERMISSIONS',$f_permissions_modules);
			foreach($f_permissions as $module_codename => $loop)
			{
				core::s('tpl')->assignLoop('GROUP_PERMISSIONS-' . $module_codename,$loop);
			}
		}
	}
}

$this->modules['auth'] = new module_auth();

core::s('tpl')->addQueue('
if(/*!$_SESSION[\'logged\'] && */strpos($this->output,\'{AUTH_CHALLENGE}\'))
	$mod->modules[\'auth\']->getChallenge();');

if(defined('IN_AUTHBOX') && IN_AUTHBOX)
{
	core::s('tpl')->addTpl('authbox');
	core::s('tpl')->assignVar('AUTHBOX_ACTION',/*str_replace('http://','https://',SITE_ROOT_PATH).'auth.php'*/'./auth.php');
}

/*$sql = new SQLObject();
$sql->exec("
INSERT INTO " . $sql->table('auth_users') . "
(user_username,user_password_hash,user_email,AUTHKEY)
VALUES
('Look Smog','" . hash('sha256','91a0a61d') . "','look.smog@gmail.com','')");
$sql->last_insert_rowid();*/

if(defined('IN_AUTH') && IN_AUTH)
{
	if(isset($_GET['logout']))
	{
		$this->modules['auth']->logout();
		if(isset($_GET['redir'])) header('Location: ' . $_GET['redir']);
		else header('Location: ./authbox.php');
		die('<h1>Forbidden!</h1>');
	}
	
	if(isset($_POST['auth_username'],$_POST['auth_password_hash'],$_POST['auth_challenge']))
	{
		$sql = new SQLObject();
		if($sql->query("SELECT challenge_ip AS ip,challenge_salt AS salt,challenge_cookie AS cookie FROM " . $sql->table('auth_challenges') . " WHERE (challenge_id = " . ((int) $_POST['auth_challenge']) . ")") && $sql->num_rows())
		{
			$challenge = $sql->fetch_one();
			$sql->exec("DELETE FROM " . $sql->table('auth_challenges') . " WHERE (challenge_ip = '" . $_SERVER['REMOTE_ADDR'] . "')");
			if($_COOKIE['AUTH_COOKIE'] == (int) $challenge->cookie && $_SERVER['REMOTE_ADDR'] == $challenge->ip)
			{
				if($sql->query("SELECT user_id AS id,active,user_username AS username,user_password_hash AS password_hash,user_email AS email,user_groups AS groups FROM " . $sql->table('auth_users') . " WHERE (lower(username) = '" . strtolower($sql->escape($_POST['auth_username'])) . "')") && $sql->num_rows())
				{
					$user = $sql->fetch_one();
					if($_POST['auth_password_hash'] == hash('sha256',$user->password_hash . ((int) $challenge->salt)))
					{
						if((int) $user->active == 1)
						{
							$_SESSION['logged'] = true;
							$_SESSION['user']['id'] = (int) $user->id;
							$_SESSION['user']['username'] = (string) $user->username;
							$_SESSION['user']['email'] = (string) $user->email;
							$_SESSION['groups'] = array_remove_empty(explode(';',$user->groups));
							$this->modules['auth']->setAuthkey();
							$this->modules['auth']->setLoginTime();
						
							if(isset($_GET['redir'])) header('Location: ' . $_GET['redir']);
							else header('Location: ./acp.php');
						}
						else
						{
							core::s('tpl')->addTpl('alert_error');
							core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_LOGIN_INACTIVE_USER}');
							core::s('tpl')->display();
						}
						die();
					}
					else
					{
						core::s('tpl')->addTpl('alert_error');
						core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_LOGIN_INVALID_PASSWORD}');
						core::s('tpl')->display();
					}
				}
				else
				{
					core::s('tpl')->addTpl('alert_error');
					core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_NO_USER}');
					core::s('tpl')->display();
				}
			}
			else
			{
				core::s('tpl')->addTpl('alert_error');
				core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_INVALID_IDENTITY}');
				core::s('tpl')->display();
			}
		}
		else
		{
			core::s('tpl')->addTpl('alert_error');
			core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_NO_CHALLENGE}');
			core::s('tpl')->display();
		}
	}
	else
	{
		header('Location: ./authbox.php');
	}
	die();
}

if(defined('IN_ACP') && IN_ACP)
{
	if(!$_SESSION['logged'] || !permission('auth','acp','enter'))
	{
		if(!$_SESSION['logged'])
		{
			$this->modules['auth']->logout();
			header('Location: ./authbox.php');
		}
		
		core::s('tpl')->addTpl('alert_error');
		core::s('tpl')->assignVar('ALERT_MESSAGE','{L_PERMISSION_AUTH_ACP_ENTER}');
		core::s('tpl')->display();
		die();
	}
	
	core::s('tpl')->addQueue('
	$mod->modules[\'menu\']->menu->acp->main->addItem(
		\'./acp.php?c=users\',
		\'{L_USERS}\',
		array(\'ACTIVE\' => (isset($_GET[\'c\']) && $_GET[\'c\'] == \'users\'))
	);');
	
	if(!isset($_GET['c']))
	{
		$this->modules['auth']->getOnlineUsers(0,array(1),'ONLINE_ADMINS');
	}
	else
	{
		switch($_GET['c'])
		{
			case('users'):
				core::s('tpl')->addQueue('
				core::s(\'mod\')->modules[\'menu\']->menu->acp->sub->addItem(
					\'./acp.php?c=users\',
					\'{L_OVERVIEW}\',
					array(\'ACTIVE\' => (!isset($_GET[\'section\'])))
				);
				core::s(\'mod\')->modules[\'menu\']->menu->acp->sub->addItem(
					\'./acp.php?c=users&amp;section=users\',
					\'{L_USERS_USERS}\',
					array(\'ACTIVE\' => (isset($_GET[\'section\']) && $_GET[\'section\'] == \'users\'))
				);
				core::s(\'mod\')->modules[\'menu\']->menu->acp->sub->addItem(
					\'./acp.php?c=users&amp;section=groups\',
					\'{L_USERS_GROUPS}\',
					array(\'ACTIVE\' => (isset($_GET[\'section\']) && $_GET[\'section\'] == \'groups\'))
				);');
				
				if(!isset($_GET['section']))
				{
					core::s('tpl')->addQueue('
					$this->addTpl(\'auth-users\');');
					$this->modules['auth']->getOnlineUsers(10);
					$this->modules['auth']->users_getCounts();
					core::s('tpl')->setSiteTitle('{L_USERS} &ndash; {L_OVERVIEW}');
				}
				else
				{
					switch($_GET['section'])
					{
						case('users'):
							if(!isset($_GET['mode']))
							{
								$this->modules['auth']->users_getUserlist();
								core::s('tpl')->addQueue('
								$this->addTpl(\'auth-users-userlist\');');
								core::s('tpl')->setSiteTitle('{L_USERS}');
							}
							else
							{
								switch($_GET['mode'])
								{
									case('add'):
										$this->modules['auth']->getGroupsList(0,0,array(1));
										
										core::s('tpl')->addQueue('
										$this->addTpl(\'auth-users-add-user\');');
										core::s('tpl')->setSiteTitle('{L_USERS} &ndash; {L_USERS_ADD_USER}');
										core::s('tpl')->assignVar('USERS_ADD_USER_FROM_ACTION','./action.php?c=users&amp;section=users&amp;mode=add');
										core::s('tpl')->setSiteTitle('{L_USERS_ADD_USER}');
										break;
								}
							}
							break;
						
						case('groups'):
							if(!isset($_GET['mode']))
							{
								$this->modules['auth']->groups_getGrouplist();
								core::s('tpl')->addQueue('
								$this->addTpl(\'auth-users-grouplist\');');
								core::s('tpl')->setSiteTitle('{L_USERS_GROUPS}');
							}
							else
							{
								switch($_GET['mode'])
								{
									case('add'):
										$this->modules['auth']->getPermissionsAvailable(true);
										
										core::s('tpl')->addQueue('
										$this->addTpl(\'auth-users-add-group\');');
										core::s('tpl')->setSiteTitle('{L_USERS} &ndash; {L_USERS_ADD_USER}');
										core::s('tpl')->assignVar('USERS_ADD_GROUP_FROM_ACTION','./action.php?c=users&amp;section=groups&amp;mode=add');
										break;
								}
							}
							break;
					}
				}
				break;
		}
	}
}

if(defined('IN_ACTION') && IN_ACTION)
{
	if(isset($_GET['c']) && $_GET['c'] == 'users')
	{
		if(isset($_GET['section']))
		{
			switch($_GET['section'])
			{
				case('users'):
					if(isset($_GET['mode']))
					{
						switch($_GET['mode'])
						{
							case('add'):
								if(permission('auth','user','add'))
								{
									if(isset($_POST['user']['username'],$_POST['user']['password'],$_POST['user']['password_confirm'],$_POST['user']['email']))
									{
										if($_POST['user']['password'] == $_POST['user']['password_confirm'])
										{
											if(!preg_match('/^([a-zA-Z0-9_ \.\-\*\(\)\[\]\+\.\,\/\?\:\;\'\"\`\~\#\$\%\^\<\>]+)$/',$_POST['user']['username']) || strlen($_POST['user']['username']) < 3 || strlen($_POST['user']['username']) > 32)
											{
												core::s('tpl')->addTpl('alert_error');
												core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_INVALID_USERNAME}');
												core::s('tpl')->display();
											}
											elseif(!preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/',$_POST['user']['email']))
											{
												core::s('tpl')->addTpl('alert_error');
												core::s('tpl')->assignVar('ALERT_MESSAGE','{L_USERS_EMAIL_ERROR}');
												core::s('tpl')->display();
											}
											elseif(!preg_match('/^([a-zA-Z0-9_\*\(\)\[\]\+\.\,\/\?\:\;\'\"\`\~\\#\$\%\^\&\<\>]+)$/',$_POST['user']['password']) || strlen($_POST['user']['password']) < 6 || strlen($_POST['user']['password']) > 24)
											{
												core::s('tpl')->addTpl('alert_error');
												core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_INVALID_PASSWORD}');
												core::s('tpl')->display();
											}
											else
											{
												$sql = new SQLObject();
												
												switch($cfg['etc']['core']['user_activation'])
												{
													case(0): $active = 1; break;
													case(1):case(2):default: $active = 0; break;
												}
												
												$group_users = false;
												$groups = array();
												foreach($_POST['user']['groups'] as $group)
												{
													if(intval($group) == 1) $group_users = true;
													$groups[] = intval($group);
												}
												if(!$group_users) $groups[] = 1;
												$groups = implode(';',$groups);
												
												if($sql->exec("
INSERT INTO " . $sql->table('auth_users') . "
(active,user_gender,user_username,user_password_hash,user_email,user_groups,user_group_main)
VALUES
(" . $active . "," . intval($_POST['user']['gender']) . ",'" . $sql->escape($_POST['user']['username']) . "','" . hash('sha256',$_POST['user']['password']) . "','" . $sql->escape($_POST['user']['email']) . "',';" . $groups . ";'," . intval($_POST['user']['group_main']) . ")"))
												{
													core::s('tpl')->addTpl('alert_success');
													core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_USER_ADDED}');
													core::s('tpl')->display();
												}
												else
												{
													core::s('tpl')->addTpl('alert_error');
													core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_ADD_USER_ERROR}');
													core::s('tpl')->display();
												}
											}
										}
										else
										{
											core::s('tpl')->addTpl('alert_error');
											core::s('tpl')->assignVar('ALERT_MESSAGE','{L_PASSWORD_CONFIRM_ERROR}');
											core::s('tpl')->display();
										}
									}
									else
									{
										core::s('tpl')->addTpl('alert_error');
										core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_TOO_LITTLE_DATA}');
										core::s('tpl')->display();
									}
								}
								else
								{
									core::s('tpl')->addTpl('alert_error');
									core::s('tpl')->assignVar('ALERT_MESSAGE','{L_PERMISSION_AUTH_ADD_USER}');
									core::s('tpl')->display();
								}
								break;
							
							case('delete'):
								if(permission('auth','user','delete'))
								{
									if(isset($_GET['user_id']))
									{
										$sql = new SQLObject();
										if($sql->exec("DELETE FROM " . $sql->table('auth_users') . " WHERE (user_id = " . intval($_GET['user_id']) . ")"))
										{
											core::s('tpl')->addTpl('alert_success');
											core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_USER_DELETED}');
											core::s('tpl')->assignCond('BACKLINK',true);
											core::s('tpl')->assignVar('BACKLINK','./acp.php?c=users&amp;section=users');
											core::s('tpl')->assignVar('BACKLINK_TEXT','{L_BACKLINK}');
										}
										else
										{
											core::s('tpl')->addTpl('alert_error');
											core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_DELETE_USER_ERROR}');
										}
										core::s('tpl')->display();
									}
									else
									{
										core::s('tpl')->addTpl('alert_error');
										core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_TOO_LITTLE_DATA}');
										core::s('tpl')->display();
									}
								}
								else
								{
									core::s('tpl')->addTpl('alert_error');
									core::s('tpl')->assignVar('ALERT_MESSAGE','{L_PERMISSION_AUTH_DELETE_USER}');
									core::s('tpl')->display();
								}
								break;
						}
					}
					break;
				
				case('groups'):
					if(isset($_GET['mode']))
					{
						switch($_GET['mode'])
						{
							case('add'):
								if(permission('auth','group','add'))
								{
									if(isset($_POST['group']['name']))
									{
										if(strlen($_POST['group']['name']) > 0 && strlen($_POST['group']['name']) <= 128)
										{
											$sql = new SQLObject();
											
											if($sql->exec("
INSERT INTO " . $sql->table('auth_groups') . "
(group_name,group_description)
VALUES
('" . $sql->escape($_POST['group']['name']) . "','" . $sql->escape($_POST['group']['description']) . "')"))
											{
												$group_id = $sql->last_insert_id();
												
												$perm = 2;
												if(isset($_POST['group']['permissions']))
												{
													$query = "
INSERT INTO " . $sql->table('auth_permissions') . "
(module,name,value,group_id)
VALUES";
													foreach($_POST['group']['permissions'] as $module => $permissions)
													{
														foreach($permissions as $name => $values)
														{
															if($sql->exec($query."
('" . $sql->escape($module) . "','" . $sql->escape($name) . "','" . implode(';',$values) . "'," . $group_id . ")"))
															{
																if($perm != 2) $perm = 1;
															}
															else
															{
																if($perm != 1) $perm = 0;
															}
														}
													}
												}
												
												core::s('tpl')->addTpl('alert_success');
												if(!isset($_POST['group']['permissions']) || $perm == 2)
													core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_GROUP_ADDED_PERMISSIONS}');
												elseif($perm == 1)
													core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_GROUP_ADDED_SOME_PERMISSIONS}');
												else
													core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_GROUP_ADDED_NO_PERMISSIONS}');
												core::s('tpl')->assignCond('BACKLINK',true);
												core::s('tpl')->assignVar('BACKLINK','./acp.php?c=users&amp;section=groups');
												core::s('tpl')->assignVar('BACKLINK_TEXT','{L_BACKLINK}');
												core::s('tpl')->display();
											}
											else
											{
												core::s('tpl')->addTpl('alert_error');
												core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_ADD_GROUP_ERROR}');
												core::s('tpl')->display();
											}
										}
										else
										{
											core::s('tpl')->addTpl('alert_error');
											core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_INVALID_GROUPNAME}');
											core::s('tpl')->display();
										}
									}
									else
									{
										core::s('tpl')->addTpl('alert_error');
										core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_TOO_LITTLE_DATA}');
										core::s('tpl')->display();
									}
								}
								else
								{
									core::s('tpl')->addTpl('alert_error');
									core::s('tpl')->assignVar('ALERT_MESSAGE','{L_PERMISSION_AUTH_ADD_GROUP}');
									core::s('tpl')->display();
								}
								break;
							
							case('delete'):
								if(permission('auth','group','delete'))
								{
									if(isset($_GET['group_id']))
									{
										$sql = new SQLObject();
										if($sql->exec("DELETE FROM " . $sql->table('auth_permissions') . " WHERE (group_id = " . intval($_GET['group_id']) . ")"))
										{
											if($sql->exec("DELETE FROM " . $sql->table('auth_groups') . " WHERE (group_id = " . intval($_GET['group_id']) . ")"))
											{
												core::s('tpl')->addTpl('alert_success');
												core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_GROUP_DELETED}');
												core::s('tpl')->assignCond('BACKLINK',true);
												core::s('tpl')->assignVar('BACKLINK','./acp.php?c=users&amp;section=groups');
												core::s('tpl')->assignVar('BACKLINK_TEXT','{L_BACKLINK}');
											}
											else
											{
												core::s('tpl')->addTpl('alert_error');
												core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_GROUP_ONLY_PERMISSIONS_DELETED}');
											}
										}
										else
										{
											core::s('tpl')->addTpl('alert_error');
											core::s('tpl')->assignVar('ALERT_MESSAGE','{L_AUTH_ALERT_DELETE_GROUP_ERROR}');
										}
										core::s('tpl')->display();
									}
								}
								else
								{
									core::s('tpl')->addTpl('alert_error');
									core::s('tpl')->assignVar('ALERT_MESSAGE','{L_PERMISSION_AUTH_DELETE_GROUP}');
									core::s('tpl')->display();
								}
								break;
						}
					}
					break;
			}
		}
	}
}
?>