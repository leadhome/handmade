jQuery(document).ready(function() {
	jQuery('[name="additional"]').click(function(){
		jQuery(this).next().toggle(500);
	});
	var prefix = 'user_form_createshop';
	jQuery('#'+prefix+'_title').keyup(function(){
		ajaxUniqueField('title',this.value,this.id);
		// jQuery.getJSON('/user/shop/ajaxuniquefield/?field=title&value=1', 
			// function(data){
				// console.log(data);
				// if(data.error==0) {
					// jQuery('#user_login_check').html('Логин свободен');
					// jQuery('#user_login_check').css('color','green');
				// } else {
					// jQuery('#user_login_check').html('Пользователь с таким логином уже существует');
					// jQuery('#user_login_check').css('color','red');
				// }
			// }
		// );	
	});
	function ajaxUniqueField(field,value,id) {
		jQuery.getJSON('/user/shop/ajaxuniquefield/?field='+field+'&value='+value, function(data){
			console.log(data);
		});
	}
});