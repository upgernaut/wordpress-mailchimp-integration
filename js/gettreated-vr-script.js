jQuery(document).ready(function($){
	
    $(window).load(function(){
	
		if(!readCookie('subscribed') && !readCookie('24hourssubscribe')) {
			
			if($('.subscribeModalBlog').length){
				$('.subscribeModalBlog').modal('show');
				createCookie('24hourssubscribe','1',1);
			}
			
		}
	
	});	

	
	$('.vr_form_blog').ajaxForm(function(jresp) { 
		console.log(jresp);
		if(jresp == 'success') {
			$('.vr_form_blog').fadeOut(function(){
				createCookie('subscribed','1',99999);
				$('.vr_thankyou').fadeIn();
			});
		} else {
			$('.vr_form_blog').fadeOut(function(){
				$('.vr_failure').fadeIn();
			});			
		}
	}); 
	
	$('.subscribeBlogModalForm').ajaxForm(function(jresp) { 
		console.log(jresp);
		if(jresp == 'success') {
			$('.subscribeBlogModalTitle').fadeOut();
			$('.subscribeBlogModalForm').fadeOut(function(){
				createCookie('subscribed','1',99999);
				$('.subscribeBlogModalThank').fadeIn();
			});
		} else {
			$('.subscribeBlogModalTitle').fadeOut();
			$('.subscribeBlogModalForm').fadeOut(function(){
				$('.subscribeBlogModalFail').fadeIn();
			});			
		}
	}); 	
	
});



function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}