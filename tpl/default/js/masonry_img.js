$(function(){
	window.onload=function(){
		var $container = $('#container');
		$container.imagesLoaded(function(){
			$container.masonry({
				itemSlector:'.case_box',
				columnWidth: 30
			});
		});
	}
})