	<h2>{L_MODULES}</h2>
	<h3>{L_MODULES_CORE}</h3>
	<table cellspacing="0">
		<tfoot>
			<tr><td colspan="2"><span class="info">{L_MODULES_CORE_FOOT}</span></td></tr>
		</tfoot>
		<tbody>
<if(!MODULES_CORE)><tr><td colspan="2" class="info">{L_MODULES_CORE_NO_MODULES}</td></tr></if(!MODULES_CORE)>
<loop(MODULES_CORE)>			<tr<if(MODULES_CORE.ACTIVE)> class="active"<else(MODULES_CORE.ACTIVE)> class="inactive"</if(MODULES_CORE.ACTIVE)>>
				<td class="icon"><img src="<var(MODULE_ICON)>" alt="[{L_ICON}]" /></td>
				<td><strong><var(MODULE_NAME)raw></strong><br /><var(MODULE_DESCRIPTION)raw></td>
			</tr>
</loop(MODULES_CORE)>		</tbody>
	</table>
	<h3>{L_MODULES_ADDITIONAL}</h3>
	<table cellspacing="0">
		<tbody>
<if(!MODULES_ADDITIONAL)><tr><td colspan="2" class="info">{L_MODULES_ADDITIONAL_NO_MODULES}</td></tr></if(!MODULES_ADDITIONAL)>
<loop(MODULES_ADDITIONAL)>			<tr<if(MODULES_ADDITIONAL.ACTIVE)> class="active"<else(MODULES_ADDITIONAL.ACTIVE)> class="inactive"</if(MODULES_ADDITIONAL.ACTIVE)>>
				<td class="icon"><img src="<var(MODULE_ICON)>" alt="[{L_ICON}]" /></td>
				<td><strong><var(MODULE_NAME)raw></strong><br /><var(MODULE_DESCRIPTION)raw></td>
				<td class="button"><if(AUTH.PERMISSION.CORE.MODULES.UNINSTALL)>
					<input type="button" onclick="modules_uninstall(this,'<var(MODULE_CODENAME)>');" value="{L_MODULE_UNINSTALL}" /></if(AUTH.PERMISSION.CORE.MODULES.UNINSTALL)>
					<if(AUTH.PERMISSION.CORE.MODULES.ACTIVATE)><if(!MODULES_ADDITIONAL.ACTIVE)><input type="button" onclick="modules_activate(this,'<var(MODULE_CODENAME)>');" value="{L_MODULE_ACTIVATE}" /><else(!MODULES_ADDITIONAL.ACTIVE)><input type="button" onclick="modules_deactivate(this,'<var(MODULE_CODENAME)>');" value="{L_MODULE_DEACTIVATE}" /></if(!MODULES_ADDITIONAL.ACTIVE)></if(AUTH.PERMISSION.CORE.MODULES.ACTIVATE)>
				</td>
				<td class="status"><img class="status-hidden" id="modules-status-<var(MODULE_CODENAME)>" />&nbsp;</td>
			</tr>
</loop(MODULES_ADDITIONAL)>		</tbody>
	</table>
	
	<div class="heading">
		<h3>{L_MODULES_AVAILABLE}</h3>
		<span class="module-get"><a href="#">{L_MODULES_GET_MORE}</a></span>
	</div>
	<table cellspacing="0">
		<tbody>
<if(!MODULES_AVAILABLE)><tr><td colspan="2" class="info">{L_MODULES_AVAILABLE_NO_MODULES}</td></tr></if(!MODULES_AVAILABLE)>
<loop(MODULES_AVAILABLE)>			<tr id="module-<var(MODULE_CODENAME)>">
				<td class="icon"><img src="<var(MODULE_ICON)>" alt="[{L_ICON}]" /></td>
				<td><strong><var(MODULE_NAME)raw></strong><br /><var(MODULE_DESCRIPTION)raw></td>
				<td class="author"><var(MODULE_AUTHOR)></td>
				<td class="button"><if(AUTH.PERMISSION.CORE.MODULES.INSTALL)><input type="button" onclick="modules_install(this,'<var(MODULE_CODENAME)>');" value="{L_MODULE_INSTALL}" /></if(AUTH.PERMISSION.CORE.MODULES.INSTALL)></td>
				<td class="status"><img class="status-hidden" id="modules-status-<var(MODULE_CODENAME)>" />&nbsp;</td>
			</tr>
</loop(MODULES_AVAILABLE)>		</tbody>
	</table>
	<div id="hider"></div>
	<div id="modules-install-dialog"></div>
	<script type="text/javascript"><!--
function modules_install(button,module_codename)
{
	var xhr = createAjaxObject();
	xhr.open('GET','./ajaxrequest.php?c=modules&function=install&module_codename='+module_codename,true);
	xhr.onreadystatechange = function()
	{
		switch(xhr.readyState)
		{
			case(1):case(2):
				document.getElementById('modules-install-dialog').innerHTML = '<pre># Initializing connection...</pre>';
				break;
			case(4):
				document.getElementById('modules-install-dialog').innerHTML = '<pre># Initializing connection... <strong>OK</strong>'+"\n"+xhr.responseText+'</pre><div id="modules-install-dialog-bottom"><input type="button" value="{L_CLOSE}" onclick="modules_dialog_close()" /></div>';
				break;
		}
	}
	button.disabled = true;
	document.getElementById('hider').style.display = 'block';
	document.getElementById('modules-install-dialog').style.display = 'block';
	document.getElementById('modules-install-dialog').innerHTML = '<pre># Initializing connection...</pre>';
	xhr.send(null);
}
function modules_uninstall(button,module_codename)
{
	var xhr = createAjaxObject();
	xhr.open('GET','./ajaxrequest.php?c=modules&function=uninstall&module_codename='+module_codename,true);
	xhr.onreadystatechange = function()
	{
		switch(xhr.readyState)
		{
			case(1):case(2):
				document.getElementById('modules-install-dialog').innerHTML = '<pre># Initializing connection...</pre>';
				break;
			case(4):
				document.getElementById('modules-install-dialog').innerHTML = '<pre># Initializing connection... <strong>OK</strong>'+"\n"+xhr.responseText+'</pre><div id="modules-install-dialog-bottom"><input type="button" value="{L_CLOSE}" onclick="modules_dialog_close()" /></div>';
				break;
		}
	}
	button.disabled = true;
	document.getElementById('hider').style.display = 'block';
	document.getElementById('modules-install-dialog').style.display = 'block';
	document.getElementById('modules-install-dialog').innerHTML = '<pre># Initializing connection...</pre>';
	xhr.send(null);
}
function modules_activate(button,module_codename)
{
	var xhr = createAjaxObject();
	xhr.open('GET','./ajaxrequest.php?c=modules&function=activate&module_codename='+module_codename,true);
	xhr.onreadystatechange = function()
	{
		switch(xhr.readyState)
		{
			case(1):case(2):
				document.getElementById('modules-status-'+module_codename).src = './styles/.acp/media/images/throbber.gif';
				document.getElementById('modules-status-'+module_codename).style.display = 'inline';
				break;
			case(4):
				if(xhr.responseText == 'OK')
				{
					document.getElementById('modules-status-'+module_codename).src = './styles/.acp/media/images/icon-ok.png';
					setTimeout('location.reload();',500);
				}
				else
					document.getElementById('modules-status-'+module_codename).src = './styles/.acp/media/images/icon-error.png';
				break;
		}
	}
	button.disabled = true;
	xhr.send(null);
}
function modules_deactivate(button,module_codename)
{
	var xhr = createAjaxObject();
	xhr.open('GET','./ajaxrequest.php?c=modules&function=deactivate&module_codename='+module_codename,true);
	xhr.onreadystatechange = function()
	{
		switch(xhr.readyState)
		{
			case(1):case(2):
				document.getElementById('modules-status-'+module_codename).src = './styles/.acp/media/images/throbber.gif';
				document.getElementById('modules-status-'+module_codename).style.display = 'inline';
				break;
			case(4):
				if(xhr.responseText == 'OK')
				{
					document.getElementById('modules-status-'+module_codename).src = './styles/.acp/media/images/icon-ok.png';
					setTimeout('location.reload();',500);
				}
				else
					document.getElementById('modules-status-'+module_codename).src = './styles/.acp/media/images/icon-error.png';
				break;
		}
	}
	button.disabled = true;
	xhr.send(null);
}
function modules_dialog_close()
{
	location.reload();
}
	--></script>
