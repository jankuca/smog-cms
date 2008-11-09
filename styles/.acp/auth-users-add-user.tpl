	<h2>{L_USERS_ADD_USER}</h2>
	<form action="{USERS_ADD_USER_FROM_ACTION}" method="post" id="form-add-user" onsubmit="return(submit_form(this,'user-submit','user-status'));">
	<table cellspacing="0">
		<tbody>
			<tr class="input-text">
				<td class="cap"><label for="input-user-username">{L_USERNAME}:</label></td>
				<td><input class="half" type="text" name="user[username]" id="input-user-username" /> <span id="input-user-username_status" class="info">{L_USERNAME_INFO}</span></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="input-user-email">{L_USERS_EMAIL}:</label></td>
				<td><input class="half" type="text" name="user[email]" id="input-user-email" /> <span id="input-user-email_status" class="info" style="display:none;">&nbsp;</span></td>
			</tr>
			<tr>
				<td class="cap">{L_USERS_GENDER}:</td>
				<td>
					<input type="radio" name="user[gender]" value="1" id="input-user-gender-male" /> <label for="input-user-gender-male">{L_GENDER_MALE}</label>
					<input type="radio" name="user[gender]" value="2" id="input-user-gender-female" /> <label for="input-user-gender-female">{L_GENDER_FEMALE}</label>
					<input type="radio" name="user[gender]" value="0" id="input-user-gender-na" checked="checked" /> <label for="input-user-gender-na">{L_GENDER_NA}</label> 
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3>{L_PASSWORD}</h3>
	<table cellspacing="0">
		<tbody>
			<tr class="input-text">
				<td class="cap"><label for="input-user-password">{L_PASSWORD}:</label></td>
				<td><input class="half" type="text" name="user[password]" id="input-user-password" /> <span id="input-user-password_status" class="info">{L_PASSWORD_INFO}</span></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="input-user-password-confirm">{L_PASSWORD_CONFIRM}:</label></td>
				<td><input class="half" type="text" name="user[password_confirm]" id="input-user-password-confirm" /> <span id="input-user-password-confirm_status" class="info" style="display:none;">&nbsp;</span></td>
			</tr>
		</tbody>
	</table>
	
	<div class="heading">
		<h3>{L_USERS_GROUPS}</h3>
		<span class="info">{L_USERS_GROUPS_INFO}</span>
	</div>
	<table cellspacing="0">
		<tbody>
			<tr>
				<td class="cap">&nbsp;</td>
				<td>
<loop(USER_GROUPS)>					<input type="checkbox" name="user[groups][]" value="<var(GROUP_ID)>" id="input-user-group-<var(GROUP_ID)>"<if(USER_GROUPS.EXCEPTION)>checked="checked" readonly="readonly" onclick="return(false);"</if(USER_GROUPS.EXCEPTION)> /><input type="radio" name="user[group_main]" value="<var(GROUP_ID)>"<if(USER_GROUPS.EXCEPTION)> checked="checked"</if(USER_GROUPS.EXCEPTION)> /> <label for="input-user-group-<var(GROUP_ID)>" title="<var(GROUP_DESCRIPTION)>"><var(GROUP_NAME)></label><br />
</loop(USER_GROUPS)>
				</td>
			</tr>
			<tr><td colspan="2"><span class="info">{L_USER_GROUP_MAIN}</span></td></tr>
			<tr>
				<td colspan="2">
					<span class="info">{L_USER_GROUPS_INFO}</span><br />
					<span class="info">{L_USER_GROUPS_MULTIPLE_INFO}</span>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="submit" id="user-submit-box">
		<input id="user-submit" type="submit" value="{L_USERS_ADD_USER_SUBMIT}" />
		<img id="user-status" class="status-hidden" />
	</div>
	</form>
	
	<script type="text/javascript" src="./app/lib/js/formValidation.js"></script>
	<script type="text/javascript"><!--
$(document).ready(function()
{
	$('#form-add-user').formValidation.init(this.id,'error','success');
	$('#input-user-username').keyup(function(){ $(this).formValidation.validateInput(this,true,'{L_USERNAME_INFO}','username',3,32); });
	$('#input-user-email').blur(function(){ $(this).formValidation.validateInput(this,true,'{L_USERS_EMAIL_ERROR}','email'); });
	
	var data = new Array();
	data['class'] = new Array();
	data['class']['invalid'] = 'error';
	data['class']['weak'] = 'password-weak';
	data['class']['normal'] = 'password-normal';
	data['class']['strong'] = 'password-strong';
	data['msg'] = new Array();
	data['msg']['invalid'] = '{L_PASSWORD_STRENGTH_INVALID}';
	data['msg']['weak'] = '{L_PASSWORD_STRENGTH_WEAK}';
	data['msg']['normal'] = '{L_PASSWORD_STRENGTH_NORMAL}';
	data['msg']['strong'] = '{L_PASSWORD_STRENGTH_STRONG}';
	$('#input-user-password').keyup(function(){ $(this).formValidation.validateInput(this,true,data,'password_strength',6,24); });
	$('#input-user-password-confirm').keyup(function(){ $(this).formValidation.validateInput(this,true,'{L_PASSWORD_CONFIRM_ERROR}','confirm','input-user-password'); });
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
