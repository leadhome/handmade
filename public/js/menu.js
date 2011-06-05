jQuery(document).ready(function(){
	$('.wrapper td').hover(
		function(){
			$(this).children('ul').attr('style','margin-left:'+($(this).children('a').width()+15)+'px');
			$(this).children('span').stop(true,true).show();			
			$(this).children('ul').stop(true,true).show();			
		},
		function(){
			$(this).children('span').stop(true,true).hide();		
			$(this).children('ul').stop(true,true).hide();
		}
	);
});