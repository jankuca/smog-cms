new function()
{
	$.fn.formValidation = {
		init: function(form_id,class_error,class_success)
		{
			this.form_id = form_id;
			this.class_error = class_error;
			this.class_success = class_success;
			this.original_statuses = new Array();
			this.original_display_modes = new Array();
		},
		validateInput: function(node,required,error_msg,type,minlength,maxlength,range)
		{
			if(!node) return(false);
			
			if(!this.original_statuses[node.id]) this.original_statuses[node.id] = $('#'+node.id+'_status').html();
			if(!this.original_display_modes[node.id]) this.original_display_modes[node.id] = $('#'+node.id+'_status').css('display');
			
	  		if(!error_msg) var error_msg = 'Contains disallowed characters!';
		  	if(!type) var type = 'any';
		  	if(!minlength) var minlength = 0;
		  	if(!maxlength) var maxlength = 0;
		  	if(!range)
		   	switch(type)
		   	{
					case('alpha'):case('alphabetic'):
						var range = /^([a-zA-Z_]+)$/; break;
					case('alphanum'):case('alphanumeric'):
						var range = /^([a-zA-Z0-9_]+)$/; break;
					case('alphanumspace'):case('alphanumericspace'):
						var range = /^([a-zA-Z0-9_ ]+)$/; break;
					case('num'):case('numeric'):
						var range = /^[(0-9)+]$/; break;
					case('email'):
						var range = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/; break;
					case('username'):
						var range = /^([a-zA-Z0-9_ \.\-\*\(\)\[\]\+\.\,\/\?\:\;\'\"\`\~\#\$\%\^\<\>]+)$/; break;
					case('passwd'):case('password'):case('password_strength'):
						var range = /^([a-zA-Z0-9_\*\(\)\[\]\+\.\,\/\?\:\;\'\"\`\~\\#\$\%\^\&\<\>]+)$/; break;
					case('any'):case('anything'):default:
						var range = /^(.*)$/; break;
				}
			
			if(type == 'password_strength')
			{
				setPasswordStrength(node,getPasswordStrength(node,minlength,maxlength),this.class_error,error_msg['class']['invalid'],error_msg['class']['weak'],error_msg['class']['normal'],error_msg['class']['strong'],error_msg['msg']['invalid'],error_msg['msg']['weak'],error_msg['msg']['normal'],error_msg['msg']['strong']);
			}
			else if(type == 'confirm')
			{
				if(minlength != 0 && node.value == document.getElementById(minlength).value)
					doSuccess(node,this.class_success,this.class_error);
				else doError(node,error_msg,this.class_error,this.class_success);
			}
			else if(
				node.value.match(range)
				&& ((minlength == 0 && required && node.value.length > minlength) || (minlength > 0 && node.value.length >= minlength))
				&& (maxlength == 0 || (maxlength > 0 && node.value.length <= maxlength))
			)
				doSuccess(node,this.class_success,this.class_error);
			else
			{
				if(!required && node.value.length == 0) doNeutral(node,this.class_success,this.class_error,this.original_statuses[node.id],this.original_display_modes[node.id]);
				else if(!required && (maxlength > 0 && node.value.length <= maxlength)) doSuccess(node,this.class_success,this.class_error);
				else doError(node,error_msg,this.class_error,this.class_success);
			}//alert(minlength + '-' + maxlength);
		}
	};

	function doSuccess(node,class_success,class_error)
	{
		$('#'+node.id).removeClass(class_error).addClass(class_success);
		$('#'+node.id+'_status').removeClass(class_error).addClass(class_success).html('&nbsp;').css({display:''});
	}
	function doError(node,error_msg,class_error,class_success)
	{
		$('#'+node.id).removeClass(class_success).addClass(class_error);
		$('#'+node.id+'_status').removeClass(class_success).addClass(class_error).html(error_msg).css({display:''});
	}
	function doNeutral(node,class_success,class_error,original_status,original_display_mode)
	{
		$('#'+node.id).removeClass(class_success).removeClass(class_error);
		$('#'+node.id+'_status').removeClass(class_success).removeClass(class_error).html(original_status).css({display:original_display_mode});
	}
	
	function getPasswordStrength(node,minlength,maxlength)
	{
		var ret = 0;
		var pass = node.value;
		//first check the length of the password
		//and check that there is no invalid characters like spaces
		var tmp = pass.search(/[^\d|a-z|A-Z|!_'-@\$#%\^\*&"~()\/\[\].:]/g);
		if(tmp == -1 && pass.length >= minlength && pass.length <= maxlength)
		{
			//check if the user not typed only one kind of characters
			reg = /^(\d+)$|^([a-z]+)$|^([A-Z]+)$|^([\W^\s]+)$/g;
			var srch = pass.search(reg);
			if(srch == -1)
			{
				//check if the user types 2 kinds of characters not in the beginning or the end
				reg = /^([\d]{1}[a-z]+)$|^([a-z]{1}[\d]+)$|^([A-Z]{1}[\W^\s]+)$|^([\W^\s]{1}[A-Z]+)$|^([\d]{1}[A-Z]+)$|^([A-Z]{1}[\d]+)$|^([a-z]{1}[\W^\s]+)$|^([\W^\s]{1}[a-z]+)$|^([\d]{1}[\W^\s]+)$|^([\W^\s]{1}[\d]+)$|^([a-z]{1}[A-Z]+)$|^([A-Z]{1}[a-z]+)$/;
				var srch = pass.search(reg);
				
				if(srch == -1)
				{
					ret = 1;
					//if more than 6 characters and the that 2 characters without the restriction above
					if(pass.length == 6)
					{
						//count the characters //it is the easy way for now
						var count_tmp = 0;
						reg =/[\d]+/g;
						var srch = pass.search(reg);
						if(srch != -1) count_tmp++;
						reg =/[a-z]+/g;
						var srch = pass.search(reg);
						if(srch != -1) count_tmp++;
						reg =/[A-Z]+/g;
						var srch = pass.search(reg);
						if(srch != -1) count_tmp++;
						reg =/[\W^\s]+/g;
						var srch = pass.search(reg);
						if(srch != -1) count_tmp++;
						if(count_tmp > 2)
						{
							ret = 2;
						}
					}
					else if(pass.length == 7) ret = 1;
					else ret = 2;
				}	
			}
		}
		else ret = -1;
		
		return ret;
	}
	function setPasswordStrength(node,strength,class_error,class_invalid,class_weak,class_normal,class_strong,msg_invalid,msg_weak,msg_normal,msg_strong)
	{
		switch(strength)
		{
			case(0):
				$('#'+node.id).addClass(class_error).addClass(class_weak).removeClass(class_normal).removeClass(class_strong);
				$('#'+node.id+'_status').addClass(class_weak).removeClass(class_normal).removeClass(class_strong).removeClass(class_invalid).html(msg_weak).css({display:''});
				break;
			case(1):
				$('#'+node.id).removeClass(class_error).removeClass(class_weak).addClass(class_normal).removeClass(class_strong);
				$('#'+node.id+'_status').removeClass(class_weak).addClass(class_normal).removeClass(class_strong).removeClass(class_invalid).html(msg_normal).css({display:''});
				break;
			case(2):
				$('#'+node.id).removeClass(class_error).removeClass(class_weak).removeClass(class_normal).addClass(class_strong);
				$('#'+node.id+'_status').removeClass(class_weak).removeClass(class_normal).addClass(class_strong).removeClass(class_invalid).html(msg_strong).css({display:''});
				break;
			case(-1):default:
				$('#'+node.id).addClass(class_error).removeClass(class_weak).removeClass(class_normal).removeClass(class_strong);
				$('#'+node.id+'_status').removeClass(class_weak).removeClass(class_normal).removeClass(class_strong).addClass(class_invalid).html(msg_invalid).css({display:''});
				break;
		}
	}
};