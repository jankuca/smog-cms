	<h2>{L_USERS} &ndash; {L_OVERVIEW}</h2>	
	<div class="boxleft">
		<h3>{L_STATS}</h3>
		<table cellspacing="0">
			<tbody>
				<tr>
					<td class="cap">{L_USERS_COUNT_USERS}:</td>
					<td>{USERS_COUNT_USERS}</td>
				</tr>
				<tr>
					<td class="cap">{L_USERS_COUNT_GROUPS}:</td>
					<td>{USERS_COUNT_GROUPS}</td>
				</tr>
			</tbody>
		</table>
		<ul>
			<li class="user-add"><a href="./acp.php?c=users&amp;section=users&amp;mode=add">{L_USERS_ADD_USER}</a></li>
			<li class="group-add"><a href="./acp.php?c=users&amp;section=groups&amp;mode=add">{L_USERS_ADD_GROUP}</a></li>
		</ul>
	</div>
	<div class="boxright">
		<h3>{L_ONLINE_USERS}</h3>
		<table cellspacing="0">
			<tbody>
<if(!ONLINE_USERS)>				<tr><td class="info">{L_ONLINE_USERS_NO_USERS}</td></tr>
<else(!ONLINE_USERS)><loop(ONLINE_USERS)>				<tr>
					<td><em class="user<if(ONLINE_USERS.ADMIN)> admin</if(ONLINE_USERS.ADMIN)>"><var(USER_USERNAME)></em> <small>(<var(USER_SESSION_TIME_OFFSET)> {L_ACRONYM_MINUTES})</small></td>
				</tr>
</loop(ONLINE_USERS)></if(!ONLINE_USERS)>			</tbody>
		</table>
	</div>
