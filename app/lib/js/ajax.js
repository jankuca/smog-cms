function createAjaxObject()
{
	var XHR = false;
	try { XHR = new XMLHttpRequest(); }
	catch(e)
	{
		var MSXmlVerze = new Array('MSXML2.XMLHttp.6.0','MSXML2.XMLHttp.5.0','MSXML2.XMLHttp.4.0','MSXML2.XMLHttp.3.0','MSXML2.XMLHttp.2.0','Microsoft.XMLHttp');
		for(var i = 0; i <= MSXmlVerze.length; i ++)
		{
			try { XHR = new ActiveXObject(MSXmlVerze[i]); break; }
			catch(e){}
		}
	}
	
	if(!XHR) alert("There was an error creating the XMLHttpRequest object!");
	else return(XHR);
}
