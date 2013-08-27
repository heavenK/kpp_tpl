$(function(){
	var sWidth = $("#slider_name").width();
	var len = $("#slider_name .silder_panel").length;
	var index = 0;
	var picTimer;
	
	$("#slider_name .silder_nav li").mouseenter(function() {																		
		index = $("#slider_name .silder_nav li").index(this);
		showPics(index);
	}).eq(0).trigger("mouseenter");

	// 
	$("#slider_name .silder_con").css("width",sWidth * (len));
	
	// mouse 
	$("#slider_name").hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == len) {index = 0;}
		},30000000); 
	}).trigger("mouseleave");
	
	// showPics
	function showPics(index) {
		var nowLeft = -index*sWidth; 
		$("#slider_name .silder_con").stop(true,false).animate({"left":nowLeft},300);
		$("#slider_name .silder_nav li").removeClass("current").eq(index).addClass("current"); 
		//$("#slider_name .silder_nav li").stop(true,false).eq(index).stop(true,false);
	}
});


$(function(){
	var sWidth = $("#slider_name_02").width();
	var len = $("#slider_name_02 .silder_panel").length;
	var index = 0;
	var picTimer;
	
	$("#slider_name_02 .silder_nav li").mouseenter(function() {																		
		index = $("#slider_name_02 .silder_nav li").index(this);
		showPics(index);
	}).eq(0).trigger("mouseenter");

	// 
	$("#slider_name_02 .silder_con").css("width",sWidth * (len));
	
	// mouse 
	$("#slider_name_02").hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == len) {index = 0;}
		},30000000); 
	}).trigger("mouseleave");
	
	// showPics
	function showPics(index) {
		var nowLeft = -index*sWidth; 
		$("#slider_name_02 .silder_con").stop(true,false).animate({"left":nowLeft},300);
		$("#slider_name_02 .silder_nav li").removeClass("current").eq(index).addClass("current"); 
		//$("#slider_name .silder_nav li").stop(true,false).eq(index).stop(true,false);
	}
});


$(function(){
	var sWidth = $("#slider_name_03").width();
	var len = $("#slider_name_03 .silder_panel").length;
	var index = 0;
	var picTimer;
	
	$("#slider_name_03 .silder_nav li").mouseenter(function() {																		
		index = $("#slider_name_03 .silder_nav li").index(this);
		showPics(index);
	}).eq(0).trigger("mouseenter");

	// 
	$("#slider_name_03 .silder_con").css("width",sWidth * (len));
	
	// mouse 
	$("#slider_name_03").hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == len) {index = 0;}
		},30000000); 
	}).trigger("mouseleave");
	
	// showPics
	function showPics(index) {
		var nowLeft = -index*sWidth; 
		$("#slider_name_03 .silder_con").stop(true,false).animate({"left":nowLeft},300);
		$("#slider_name_03 .silder_nav li").removeClass("current").eq(index).addClass("current"); 
		//$("#slider_name .silder_nav li").stop(true,false).eq(index).stop(true,false);
	}
});



$(function(){
	var sWidth = $("#slider_name_04").width();
	var len = $("#slider_name_04 .silder_panel").length;
	var index = 0;
	var picTimer;
	
	$("#slider_name_04 .silder_nav li").mouseenter(function() {																		
		index = $("#slider_name_04 .silder_nav li").index(this);
		showPics(index);
	}).eq(0).trigger("mouseenter");

	// 
	$("#slider_name_04 .silder_con").css("width",sWidth * (len));
	
	// mouse 
	$("#slider_name_04").hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == len) {index = 0;}
		},30000000); 
	}).trigger("mouseleave");
	
	// showPics
	function showPics(index) {
		var nowLeft = -index*sWidth; 
		$("#slider_name_04 .silder_con").stop(true,false).animate({"left":nowLeft},300);
		$("#slider_name_04 .silder_nav li").removeClass("current").eq(index).addClass("current"); 
		//$("#slider_name .silder_nav li").stop(true,false).eq(index).stop(true,false);
	}
});