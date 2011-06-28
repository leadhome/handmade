jQuery(document).ready(function() {
	jQuery('[name="addproducttocart"]').bind('click',function(event) {
		event.preventDefault();
		jQuery('.module_cart div').children('a').parent().append('<div class="preloaderCart"></div>');
		var url = jQuery(this).attr('href');
		jQuery.getJSON(url, function(data){
			if(data.error==0) {
				jQuery('#cart_product_counts').text(data.counts);
				jQuery('#cart_product_total').text(number_format(data.total, 2, '.', ' '));
			} else {
				alert(data.message);
			}
		});
		jQuery('.preloaderCart').fadeOut('300',function() {
			jQuery('.preloaderCart').remove();
		});
	});		
});

function number_format( number, decimals, dec_point, thousands_sep ) { 
	var i, j, kw, kd, km;
	if( isNaN(decimals = Math.abs(decimals)) ){
		decimals = 2;
	}
	if( dec_point == undefined ){
		dec_point = ",";
	}
	if( thousands_sep == undefined ){
		thousands_sep = ".";
	}	 
	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";
	if( (j = i.length) > 3 ){
		j = j % 3;
	} else{
		j = 0;
	}	 
	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");	 
	return km + kw + kd;
}