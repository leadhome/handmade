jQuery(document).ready(function() {
	var materials = new Array();
	var tags = new Array();
	var form_name = 'product_form_index_add';
	var prefix = form_name+'_';	
	
	//добавление default категориям
	var default_option = '<option value="0" selected="selected">Выберите категорию</option>';
	jQuery('#'+prefix+'categories').prepend(default_option)
	//выбор категории
	jQuery('#'+prefix+'categories').change( function(){
            var subCategories = jQuery('#'+prefix+'subCategories');
                    if(this.value==0){
                            subCategories.html(default_option);
                            return false;
                    }
            jQuery.getJSON('/product/index/getcategories/?parent_id='+this.value, function(data) {
                subCategories.html('');
                jQuery.each(data.categories, function(i){
                    subCategories.append('<option value="' + i + '">' + this + '</option>');
                });
            });
        });
	
	//выбор цвета
	jQuery('[name="color[]"]').change(function(){
		if(this.value == 0) return false;
		var last_val = jQuery(this).data('last_val');
		var value = jQuery(this).val();    
		var id = jQuery(this).attr('id');  
		jQuery(this).data('last_val',value);		
		jQuery.each(jQuery('[name="color[]"]'),function(i,child){
			if(jQuery(child).attr('id')!=id) {
				jQuery("#"+jQuery(child).attr('id')+" > option[value='"+value+"']").hide();
				jQuery("#"+jQuery(child).attr('id')+" > option[value='"+last_val+"']").show();
			}
		});
	});
	
	//состояние товара
	jQuery('#'+prefix+'availlable_id').change( function(){
            if(this.value == 1) jQuery('#'+prefix+'quantity').attr('style', 'display:inline');
            else jQuery('#'+prefix+'quantity').attr('style', 'display:none');
        });
	
	//показ своих тэгов
	jQuery('[name="showTags"]').click(function() {
		jQuery(this).next().stop(true, true).toggle('500');
		if(jQuery(this).data('status')=='up') {
			jQuery(this).text('Показать все мои теги');
			jQuery(this).data('status','down');
		} else {
			jQuery(this).text('Скрыть');
			jQuery(this).data('status','up');
		}
	});
	
	//Выбор главной картинки
	jQuery('[name="isMainPhoto"]').live('click',function(){
		photos['main'] = this.id;
	})
	
	//добавление материалов и тэгов
	jQuery('[name="add_mark"]').click(function(event) {
		event.preventDefault();
		addMark(this.id,jQuery(this).next().attr('id'),jQuery(this).prev().val());
	}); 
	
	//удаление материалов и тэгов
	jQuery('[name="delete_mark"]').live('click',function() {
		var value = jQuery(this).prev().text();
		if(jQuery(this).parent().parent().attr('id')=='selected_materials') var data = materials;
		else var data = tags;	
		
		for (var i=0; i<=data.length-1; i++){
			if(data[i]==value) break;
		}
		data.splice(i,1);
		jQuery(this).parent().remove();
	});
	jQuery('[name="add_tag"]').click(function(event){
		event.preventDefault();
		addMark('add_tag','selected_tags',jQuery(this).text(),true);
	});
	
	//валидация формы
	jQuery('[name="submit_validator"]').live('click',function(event) {
		event.preventDefault();
		var form =  jQuery('#'+form_name);
		var form_id = jQuery(form).attr('id');
		var action = jQuery(form).attr('action');
		jQuery(this).after('<div class="preloader"><span>Идет проверка</span></div>');
		
		jQuery.post(action, jQuery(form).serialize(),function(data){
			if(data.error==0) {
				jQuery('#'+prefix+'materials').val(serialize(materials));
				jQuery('#'+prefix+'tags').val(serialize(tags));

				jQuery.each(photos['lists'],function(key,photo){
					photos['lists'][key]['desc'] = jQuery('#'+photo['name'].replace(/\./,'\\.')).parent().parent().next().children('input').val();
				});
				jQuery('#'+prefix+'photos').val(serialize(photos));
				jQuery(form).submit();
			}
			jQuery('#'+form_id+ ' .not_valid').removeClass('not_valid');
			jQuery('#'+form_id+ ' .errors').remove();
			if(jQuery('#'+prefix+'subCategories').val()==0) jQuery('#'+prefix+'subCategories').addClass('not_valid');
			jQuery.each(data.error,function(key,err){
				jQuery('#'+prefix+key).addClass('not_valid');
			})
			jQuery('.preloader').remove();
		});		
	});
	
        //это не гавнокод
        jQuery('#select_tag').keypress(function(event){
            if(event.which == 13){
                event.preventDefault();
                addMark('add_tag', 'selected_tags', jQuery(this).val());
                jQuery("#"+jQuery(this).id).autocomplete({"source":"\/product\/index\/ajaxeditproductautocomplete\/?type=tag"});
            }
        });
	
	//фукнция добавления меток
	function addMark(id,id_marks,value,simple_tag) {
		var value = jQuery.trim(value);
		if(value=='') {			
			if(!jQuery('#'+id).prev().hasClass('not_valid')) {
				jQuery('#'+id).prev().addClass('not_valid')
			}			
			return false;
		}
		var error = 0;			
		if(id=='add_material') var data = materials;
		else var data = tags;			
		jQuery.each(data,function(i,result){
			if(value==result) error++;
		});
		if(error>0) {
			if(simple_tag!=true) {
				if(!jQuery('#'+id).prev().hasClass('not_valid')) {
					jQuery('#'+id).prev().addClass('not_valid')
				}
			}
			return false;
		}
		jQuery('#'+id).prev().removeClass('not_valid');
		data.push(value);
		if(jQuery('#'+id_marks).children('span').length!=0) {
			jQuery(jQuery('#'+id_marks).children('span')[((jQuery('#'+id_marks).children('span').length)-1)]).append(', ')
		}
		jQuery('#'+id_marks).append('<span><span>'+value+'</span><a href="javascript:void(0)" name="delete_mark">Удалить</a></span>');
		jQuery('#'+id).prev().val('');
	}
});