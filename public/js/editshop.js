jQuery(document).ready(function() {
	jQuery('[name="additional"]').click(function(){
		jQuery(this).next().toggle(500);
	});
	var prefix = 'user_form_editshop';
	jQuery('#'+prefix+'_title').keyup(function(){
		ajaxUniqueField('title',this.value,this.id);
	});
	jQuery('#'+prefix+'_domain').keyup(function(){
		// if(checkURL(this.value+'.'+window.location.hostname)) {
		// alert(this.value+'.'+window.location.hostname+'.ru');
		// if(checkURL(this.value+'.'+window.location.hostname+'.ru')) {
			// jQuery('#'+this.id).parent().next().html('Поле заполнено не корректно');
			// return false;
		// }
		ajaxUniqueField('domain',this.value,this.id);
	});
	function checkURL(url) {
		var regURL = /^(?:(?:https?|ftp|telnet):\/\/(?:[a-z0-9_-]{1,32}(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|org|mil|edu|arpa|ru|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:\/[a-z0-9.,_@%&?+=\~\/-]*)?(?:#[^ \'\"&<>]*)?$/i;
		return regURL.test(url);
	}
	function ajaxUniqueField(field,value,id) {
		if(value=='') {
			jQuery('#'+id).parent().next().html('');
			return false;
		}
		jQuery('#'+id).parent().next().html('<div class="checkUniqueField">Проверка...</div>');
		jQuery.getJSON('/user/shop/ajaxuniquefield/?field='+field+'&value='+value, function(data){
			if(data.error==0) {
				var message = '<div class="checkUniqueField">свободно</div>';				
			} else {
				var message = '<ul class="errors">'
				jQuery.each(data.error,function(i,err){
					message += '<li>'+err+'</li>';
				});
				message += '</ul>'
				jQuery('#'+id).parent().next().children('div.checkUniqueField').remove();
			}
			if(jQuery('#'+prefix+'_'+field).val()=='') return false;
			jQuery('#'+id).parent().next().html(message);
		});
	}
});