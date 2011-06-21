jQuery(document).ready(function() {
	jQuery('[name="submit_validator"]').bind('click',function(event) {
		event.preventDefault();
		var form = jQuery(this).parents('form');
		var form_id = jQuery(form).attr('id');
		var action = jQuery(form).attr('action');
		
		jQuery(this).parent().next().html('<div class="preloader"><span>Идет проверка</span></div>');
		
		jQuery.post(action, jQuery(form).serialize(),function(data){
			if(data.error==0) jQuery(form).submit();
			jQuery('#'+form_id+ ' .not_valid').removeClass('not_valid');
			jQuery('#'+form_id+ ' .errors').remove();
			jQuery.each(data.error,function(key,err){
				var i = '';
				var token = '<br/>';
				var textErrors = '';
				var Ttoken = '';
				for (i in err) { 
					textErrors += Ttoken + err[i];
					Ttoken = token;
				}
				if(!jQuery('#'+form_id+'_'+key).hasClass('not_valid')) {
					jQuery('#'+form_id+'_'+key).addClass('not_valid');
					jQuery('#'+form_id+'_'+key).parent().next().html('<div class="errors">'+textErrors+'</div>');
				}			
			})
			jQuery('#'+form_id+ ' .preloader').remove();
		});		
	});		
});