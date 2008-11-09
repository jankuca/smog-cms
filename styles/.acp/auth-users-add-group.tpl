	<h2>{L_USERS_ADD_GROUP}</h2>
	<form action="{USERS_ADD_GROUP_FROM_ACTION}" method="post" id="form-add-group" onsubmit="return(submit_form(this,'user-submit','user-status'));">
	<table cellspacing="0">
		<tbody>
			<tr class="input-text">
				<td class="cap"><label for="input-user-username">{L_GROUP_NAME}:</label></td>
				<td><input class="half" type="text" name="group[name]" id="input-group-name" /> <span id="input-group-name_status" class="info">{L_GROUP_NAME_INFO}</span></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="input-group-description">{L_GROUP_DESCRIPTION}:</label></td>
				<td><textarea rows="3" cols="64" class="half" name="group[description]" id="input-group-description"></textarea> <span id="input-group-description_status" class="info" style="vertical-align: top;position:relative; top: 6px;">{L_GROUP_DESCRIPTION_INFO}</span></td>
			</tr>
		</tbody>
	</table>
	
	<div class="heading">
		<h3>{L_GROUP_PERMISSIONS}</h3>
		<span class="info">{L_GROUP_PERMISSIONS_INFO}</span>
	</div>
	<table cellspacing="0">
		<tbody>
<loop(GROUP_PERMISSIONS)><loop(GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>)>
			<tr class="input-checkboxes">
				<if(GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>.FIRST)><td class="cap" rowspan="<length(GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>)>"><label><var(PERMISSIONS_MODULE)></label></td></if(GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>.FIRST)>
				<td class="cap"><var(PERMISSIONS_NAME)></td>
				<td>
					<loop(GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>-<var(PERMISSIONS_CODENAME)>)><input type="checkbox" name="group[permissions][<var(PERMISSIONS_MODULE_CODENAME)>][<var(PERMISSIONS_CODENAME)>][]" value="<var(PERMISSION_VALUE)>" id="checkbox-<var(PERMISSIONS_MODULE_CODENAME)>-<var(PERMISSIONS_CODENAME)>-<var(PERMISSION_VALUE)>" />
					<label for="checkbox-<var(PERMISSIONS_MODULE_CODENAME)>-<var(PERMISSIONS_CODENAME)>-<var(PERMISSION_VALUE)>"><var(PERMISSION_NAME)></label><if(!GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>-<var(PERMISSIONS_CODENAME)>.LAST)>, </if(!GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>-<var(PERMISSIONS_CODENAME)>.LAST)>
					</loop(GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>-<var(PERMISSIONS_CODENAME)>)>
				</td>
			</tr>
</loop(GROUP_PERMISSIONS-<var(PERMISSIONS_MODULE_CODENAME)>)></loop(GROUP_PERMISSIONS)>
		</tbody>
	</table>

	<div class="submit" id="user-submit-box">
		<input id="user-submit" type="submit" value="{L_USERS_ADD_GROUP_SUBMIT}" />
		<img id="user-status" class="status-hidden" />
	</div>
	</form>
	
	<script type="text/javascript" src="./app/lib/js/formValidation.js"></script>
	<script type="text/javascript"><!--
$(document).ready(function()
{
	$('#form-add-group').formValidation.init(this.id,'error','success');
	$('#input-group-name').keyup(function(){ $(this).formValidation.validateInput(this,true,'{L_GROUP_NAME_INFO}','string',1,128); });
	$('#input-group-description').keyup(function(){ $(this).formValidation.validateInput(this,false,'{L_GROUP_DESCRIPTION_INFO}','string',0,256); });
});

function submit_form(formNode,submitId,statusId)
{
	document.getElementById(submitId).disabled = true;
	document.getElementById(statusId).className = '';
	document.getElementById(statusId).src = './styles/.acp/media/images/throbber.gif';
	formNode.submit();
	return(false);
}
	--></script>
