	<h2>{L_USERS_GROUPS}</h2>
	
	<div class="options">
		<ul>
			<li class="group-add"><a href="./acp.php?c=users&amp;section=groups&amp;mode=add">{L_USERS_ADD_GROUP}</a></li>
		</ul>
		{PAGES_GROUPLIST}
	</div>
	
	<table cellspacing="0" class="list">
		<tbody>
<loop(AUTH_GROUPLIST)>			<tr>
				<td class="icon"><img src="./styles/.acp/media/images/icon-group.png" alt="[...]" /></td>
				<td><strong><var(GROUP_NAME)></strong><br /><var(GROUP_DESCRIPTION)></td>
				<td class="action"><a href="<var(GROUP_ACTION_EDIT)>">{L_EDIT}</a></td>
				<td class="action"><if(!AUTH_GROUPLIST.ANONYMOUS)><a href="<var(GROUP_ACTION_DELETE)>">{L_DELETE}</a></if(!AUTH_GROUPLIST.ANONYMOUS)></td>
			</tr>
</loop(AUTH_GROUPLIST)>		</tbody>
	</table>
	
	<div class="options">{PAGES_GROUPLIST}</div>
