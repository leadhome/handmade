jQuery(document).ready(function() {
	if(price_delivery.length!=0) {
		jQuery.each(price_delivery,function(i,price){
			jQuery('#'+prefix+'_delivery-'+i).parent('label').after('<input name="price_delivery['+i+']" type="text" value="'+price+'"/>');
		});
	}
	jQuery('[name="additional"]').click(function(){
		jQuery(this).next().toggle(500);
	});
	jQuery('#'+prefix+'_title').keyup(function(){
		ajaxUniqueField('title',this.value,this.id);
	});
	jQuery('#'+prefix+'_domain').keyup(function(){
		ajaxUniqueField('domain',this.value,this.id);
	});
	jQuery('[name="delivery[]"]').click(function(){
		var key = this.id;
			key = key.substr(-1);
		if(this.checked==true) {
			if(jQuery('#'+this.id).parent('label').next('input[name="price_delivery['+key+']"]').length==0) 
				jQuery('#'+this.id).parent('label').after('<input name="price_delivery['+key+']" type="text" value=""/>');
			else jQuery('#'+this.id).parent('label').next('input[name="price_delivery['+key+']"]').show();
		} else jQuery('#'+this.id).parent('label').next('input[name="price_delivery['+key+']"]').hide();
	});
	function ajaxUniqueField(field,value,id) {
		if(value=='') {
			jQuery('#'+id).parent().next().html('');
			return false;
		}
		jQuery('#'+id).parent().next().html('<div class="checkUniqueField">Проверка...</div>');
		jQuery.getJSON('/user/shop/ajaxuniquefield/?field='+field+'&value='+value, function(data){
			if(data.error==0) {
				jQuery('#'+id).removeClass('not_valid');
				var message = '<div class="checkUniqueField">свободно</div>';				
			} else {
				if(!jQuery('#'+id).hasClass('not_valid')) jQuery('#'+id).addClass('not_valid');
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