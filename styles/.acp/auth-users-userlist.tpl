	<h2>{L_USERS}</h2>
	
	<div class="options">
		<ul>
			<li class="user-add"><a href="./acp.php?c=users&amp;section=users&amp;mode=add">{L_USERS_ADD_USER}</a></li>
		</ul>
		{PAGES_USERLIST}
	</div>
	
	<table cellspacing="0" class="list">
		<tbody>
<loop(AUTH_USERLIST)>			<tr>
				<td class="icon"><if(AUTH_USERLIST.USER_GENDER_MALE)><img src="./styles/.acp/media/images/icon-user-male.png" alt="[male]" /></if(AUTH_USERLIST.USER_GENDER_MALE)><if(AUTH_USERLIST.USER_GENDER_FEMALE)><img src="./styles/.acp/media/images/icon-user-female.png" alt="[female]" /></if(AUTH_USERLIST.USER_GENDER_FEMALE)><if(AUTH_USERLIST.USER_GENDER_NA)><img src="./styles/.acp/media/images/icon-user-na.png" alt="[N/A]" /></if(AUTH_USERLIST.USER_GENDER_NA)></td>
				<td><var(USER_USERNAME)></td>
				<td class="author"><var(USER_GROUP_NAME)></td>
				<td class="action"><a href="<var(USER_ACTION_EDIT)>">{L_EDIT}</a></td>
				<td class="action"><a href="<var(USER_ACTION_DELETE)>">{L_DELETE}</a></td>
			</tr>
</loop(AUTH_USERLIST)>		</tbody>
	</table>
	
	<div class="options">{PAGES_USERLIST}</div>
