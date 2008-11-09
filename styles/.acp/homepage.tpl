	<h2>{L_ACP_HOME}</h2>
	<div class="boxright">
		<h3>{L_SYSTEM_INFO}</h3>
		<table cellspacing="0">
			<tbody>
				<tr>
					<td class="cap">{L_INFO_CORE_VERSION}:</td>
					<td>{INFO:CORE_VERSION}</td>
				</tr>
				<tr>
					<td class="cap">{L_INFO_CORE_LAST_UPDATE}:</td>
					<td>{INFO:CORE_LAST_UPDATE}</td>
				</tr>
				<tr>
					<td colspan="2" class="center" id="updater-status"><input type="button" value="{L_UPDATER_CHECK_FOR_UPDATES}" onclick="updater_checkforupdates()" /></td>
				</tr>
			</tbody>
		</table>
		
		<h3>{L_ONLINE_ADMINS}</h3>
		<table cellspacing="0">
			<tbody>
<if(!ONLINE_ADMINS)>				<tr><td class="info">{L_ONLINE_ADMINS_NO_ADMINS}</td></tr>
<else(!ONLINE_ADMINS)><loop(ONLINE_ADMINS)>				<tr>
					<td><em class="user admin"><var(USER_USERNAME)></em> <small>(<var(USER_SESSION_TIME_OFFSET)> {L_ACRONYM_MINUTES})</small></td>
				</tr>
</loop(ONLINE_ADMINS)></if(!ONLINE_ADMINS)>			</tbody>
		</table>
	</div>
