	<h2>{L_SITE_CONFIG}</h2>
	<table cellspacing="0">
		<tbody>
			<tr class="input-text">
				<td class="cap"><label for="config-base-site-header">{L_CONFIG_BASE_SITE_HEADER}:</label></td>
				<td><input type="text" name="config[core][site_header]" id="config-base-site-header" value="{MODULE_CONFIG:SITE_HEADER}" /></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-site-slogan">{L_CONFIG_BASE_SITE_SLOGAN}:</label></td>
				<td><input type="text" name="config[core][site_slogan]" id="config-base-site-slogan" value="{MODULE_CONFIG:SITE_SLOGAN}" /></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-site-root-path">{L_CONFIG_BASE_SITE_ROOT_PATH}:</label></td>
				<td><input class="half" type="text" name="config[core][site_root_path]" id="config-base-site-root-path" value="{MODULE_CONFIG:SITE_ROOT_PATH}" /> <span class="info">{L_CONFIG_BASE_SITE_ROOT_PATH_INFO}</span></td>
			</tr>
		</tbody>
	</table>
	
	<h3>{L_CONFIG_BASE_OPTIONS}</h3>
	<table cellspacing="0">
		<tbody>
			<tr>
				<td class="cap">{L_CONFIG_BASE_SHOW_ERRORS}:</td>
				<td>
					<input type="radio" name="config[core][show_errors]" value="1" id="config-base-show-errors-true"<if(MODULE_CONFIG:SHOW_ERRORS)> checked="checked"</if(MODULE_CONFIG:SHOW_ERRORS)> /> <label for="config-base-show-errors-true">{L_YES}</label>
					<input type="radio" name="config[core][show_errors]" value="0" id="config-base-show-errors-false"<if(!MODULE_CONFIG:SHOW_ERRORS)> checked="checked"</if(!MODULE_CONFIG:SHOW_ERRORS)> /> <label for="config-base-show-errors-false">{L_NO}</label>
				</td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-site-lang">{L_CONFIG_BASE_SITE_LANG}:</label></td>
				<td>
					<select name="config[core][site_lang]" id="config-base-site-lang">
<loop(MODULE_CONFIG:SITE_LANG)>						<option value="<var(LANG_CODENAME)>"<if(MODULE_CONFIG:SITE_LANG.LANG_ACTIVE)> selected="selected"</if(MODULE_CONFIG:SITE_LANG.LANG_ACTIVE)>><var(LANG_CODENAME)></option>
</loop(MODULE_CONFIG:SITE_LANG)>					</select>
				</td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label>{L_CONFIG_BASE_SITE_STYLE}:</label></td>
				<td>
					<input class="half" name="config[core][site_style]" id="config-base-site-style" style="display:none;" />
					<div id="stylesSelect" style="float:left"></div>
					<span class="info_right link-add"><a href="#">{L_GET_MORE_STYLES}</a></span>
				</td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-acp-lang">{L_CONFIG_BASE_ACP_LANG}:</label></td>
				<td>
					<select name="config[core][acp_lang]" id="config-base-acp-lang">
<loop(MODULE_CONFIG:ACP_LANG)>						<option value="<var(LANG_CODENAME)>"<if(MODULE_CONFIG:ACP_LANG.LANG_ACTIVE)> selected="selected"</if(MODULE_CONFIG:ACP_LANG.LANG_ACTIVE)>><var(LANG_CODENAME)></option>
</loop(MODULE_CONFIG:ACP_LANG)>					</select>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3>{L_CONFIG_BASE_DATE_TIME}</h3>
	<table cellspacing="0">
		<tbody>
			<tr class="input-text">
				<td class="cap"><label for="config-base-datetime-format">{L_CONFIG_BASE_DATETIME_FORMAT}:</label></td>
				<td><input class="half" type="text" name="config[core][datetime_format]" id="config-base-datetime-format" value="{MODULE_CONFIG:DATETIME_FORMAT}" /> <span class="info">{L_CONFIG_BASE_DATETIME_FORMAT_INFO}</span></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-date-format">{L_CONFIG_BASE_DATE_FORMAT}:</label></td>
				<td><input class="half" type="text" name="config[core][date_format]" id="config-base-date-format" value="{MODULE_CONFIG:DATE_FORMAT}" /> <span class="info">{L_CONFIG_BASE_DATETIME_FORMAT_INFO}</span></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-online-offset">{L_CONFIG_BASE_ONLINE_OFFSET}:</label></td>
				<td><input class="numeric" type="text" name="config[core][online_offset]" id="config-base-online-offset" value="{MODULE_CONFIG:ONLINE_OFFSET}" /> <span class="info">{L_CONFIG_BASE_ONLINE_OFFSET_INFO}</span></td>
			</tr>
		</tbody>
	</table>
	
	<div class="heading">
		<h3>{L_CONFIG_BASE_FTP_DATA}</h3>
		<span class="info">{L_CONFIG_BASE_FTP_DATA_INFO}</span
	</div>
	<table cellspacing="0">
		<tbody>
			<tr class="input-text">
				<td class="cap"><label for="config-base-ftp-host">{L_CONFIG_BASE_FTP_HOST}:</label></td>
				<td><input class="half" type="text" name="config[core][ftp_host]" id="config-base-ftp-host" value="{MODULE_CONFIG:FTP_HOST}" /> <span class="info">{L_CONFIG_BASE_FTP_HOST_INFO}</span></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-ftp-port">{L_CONFIG_BASE_FTP_PORT}:</label></td>
				<td><input class="numeric" type="text" name="config[core][ftp_port]" id="config-base-ftp-port" value="{MODULE_CONFIG:FTP_PORT}" /> <span class="info">{L_CONFIG_BASE_FTP_PORT_INFO}</span></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-ftp-username">{L_CONFIG_BASE_FTP_USERNAME}:</label></td>
				<td><input class="half" type="text" name="config[core][ftp_username]" id="config-base-ftp-username" value="{MODULE_CONFIG:FTP_USERNAME}" /></td>
			</tr>
			<tr class="input-text">
				<td class="cap"><label for="config-base-ftp-password">{L_CONFIG_BASE_FTP_PASSWORD}:</label></td>
				<td><input class="half" type="password" name="config[core][ftp_password]" id="config-base-ftp-password" value="{MODULE_CONFIG:FTP_PASSWORD}" /></td>
			</tr>
		</tbody>
	</table>
	
	<div class="submit" id="config-base-submit-box">
		<input id="config-base-submit" type="submit" value="{L_CONFIG_SUBMIT}" />
		<img id="config-base-status" class="status-hidden" /><span id="config-base-status-text"></span>
	</div>
	
	<script type="text/javascript" src="./app/lib/js/customSelect.js"></script>
	<script type="text/javascript"><!--
var ConfigForm = function(inputs)
{
	this.action = false
	
	this.submitForm = function()
	{
		var button = this;
		
		document.getElementById('config-base-site-style').value = stylesSelect.getValue();
		
		var xhr = createAjaxObject();
		xhr.open('POST','./ajaxrequest.php?c=config&module=core',true);
		xhr.onreadystatechange = function()
		{
			switch(xhr.readyState)
			{
				case(4):
					if(xhr.responseText == 'OK')
					{
						button.disabled = false;
						document.getElementById('config-base-status').src = './styles/.acp/media/images/icon-ok.png';
						document.getElementById('config-base-status-text').innerHTML = ' {L_CONFIG_OK}';
						Fat.fade_element('config-base-submit-box',false,1000,'#AAFF55');
					}
					else if(xhr.responseText == 'AUTH')
					{
						document.getElementById('config-base-status').src = './styles/.acp/media/images/icon-error.png';
						document.getElementById('config-base-status-text').innerHTML = ' {L_CONFIG_AUTH}';
						Fat.fade_element('config-base-submit-box',false,1000,'#FFAA88','#FFDDBB');
					}
					else
					{
						document.getElementById('config-base-status').src = './styles/.acp/media/images/icon-error.png';
						document.getElementById('config-base-status-text').innerHTML  = ' {L_CONFIG_ERROR}';
						Fat.fade_element('config-base-submit-box',false,1000,'#FFAA88','#FFDDBB');
					}
				break;
			}
		}
		
		button.disabled = true;
		document.getElementById('config-base-status').className = '';
		document.getElementById('config-base-status').src = './styles/.acp/media/images/throbber.gif';
		
		
		var params = '';
		for(var i in inputs)
		{
			if(typeof inputs[i] == 'object')
			{
				var name = document.getElementById(inputs[i][0]).name;
				
				if(document.getElementById(inputs[i][0]).checked) var value = 0;
				else var value = 1;
			}
			else
			{
				var name = document.getElementById(inputs[i]).name;
				var value = document.getElementById(inputs[i]).value;
			}
			
			params += name+'='+value+'&';
		}
		
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.setRequestHeader("Content-length", params.length);
      xhr.setRequestHeader("Connection", "close");
		xhr.send(params);
	}
	
	this.init = function(submit_id)
	{
		document.getElementById(submit_id).onclick = this.submitForm;
	}
}

var form_inputs = new Array();
form_inputs['site_header'] = 'config-base-site-header';
form_inputs['site_slogan'] = 'config-base-site-slogan';
form_inputs['site_root_path'] = 'config-base-site-root-path';
form_inputs['show_errors'] = new Array('config-base-show-errors-false','config-base-show-errors-true');
form_inputs['site_lang'] = 'config-base-site-lang';
form_inputs['site_style'] = 'config-base-site-style';
form_inputs['acp_lang'] = 'config-base-acp-lang';
form_inputs['datetime_format'] = 'config-base-datetime-format';
form_inputs['date_format'] = 'config-base-date-format';
form_inputs['online_offset'] = 'config-base-online-offset';
form_inputs['ftp_host'] = 'config-base-ftp-host';
form_inputs['ftp_port'] = 'config-base-ftp-port';
form_inputs['ftp_username'] = 'config-base-ftp-username';
form_inputs['ftp_password'] = 'config-base-ftp-password';

var theForm = new ConfigForm(form_inputs);
theForm.init('config-base-submit');

var stylesSelect = $("#stylesSelect").finalselect({id:"config-base-site-style",zIndex:1,viewWidth:'276px',viewHeight:'192px',selectText:'{MODULE_CONFIG:SITE_STYLE_NAME}'});

<loop(MODULE_CONFIG:SITE_STYLE)>
stylesSelect.addItem('<img style="float:left;background:#FFF;border: 1px solid #888;margin: 2px;padding: <if(MODULE_CONFIG:SITE_STYLE.STYLE_PREVIEW)>1px<else(MODULE_CONFIG:SITE_STYLE.STYLE_PREVIEW)>25px 33px</if(MODULE_CONFIG:SITE_STYLE.STYLE_PREVIEW)>;" src="<var(STYLE_PREVIEW)>" /><div class="customSelectItem"><span class="thistext"><var(STYLE_NAME)></span><br /><small>{L_AUTHOR}: <if(MODULE_CONFIG:SITE_STYLE.STYLE_AUTHOR_LINK)><a href="<var(STYLE_AUTHOR_LINK)>" target="_blank"><var(STYLE_AUTHOR)></a><else(MODULE_CONFIG:SITE_STYLE.STYLE_AUTHOR_LINK)><var(STYLE_AUTHOR)></if(MODULE_CONFIG:SITE_STYLE.STYLE_AUTHOR_LINK)></small></div><div class="clear"></div>','<var(STYLE_CODENAME)>');
</loop(MODULE_CONFIG:SITE_STYLE)>
	--></script>
