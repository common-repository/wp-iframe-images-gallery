function iframe_submit()
{
	if(document.iframe_form.iframe_path.value == "")
	{
		alert(iframe_adminscripts.iframe_path);
		document.iframe_form.iframe_path.focus();
		return false;
	}
	else if(document.iframe_form.iframe_link.value == "")
	{
		alert(iframe_adminscripts.iframe_link);
		document.iframe_form.iframe_link.focus();
		return false;
	}
	else if((document.iframe_form.iframe_order.value != "") && isNaN(document.iframe_form.iframe_order.value))
	{
		alert(iframe_adminscripts.iframe_order);
		document.iframe_form.iframe_order.focus();
		return false;
	}
	else if(document.iframe_form.iframe_type.value == "" || document.iframe_form.iframe_type.value == "Select")
	{
		alert(iframe_adminscripts.iframe_type);
		document.iframe_form.iframe_type.focus();
		return false;
	}
}

function iframe_delete(id)
{
	if(confirm(iframe_adminscripts.iframe_delete))
	{
		document.frm_iframe_display.action="options-general.php?page=iframe-images-gallery&ac=del&did="+id;
		document.frm_iframe_display.submit();
	}
}	

function iframe_redirect()
{
	window.location = "options-general.php?page=iframe-images-gallery";
}

function iframe_help()
{
	window.open("http://www.gopiplus.com/work/2011/07/24/wordpress-plugin-wp-iframe-images-gallery/");
}