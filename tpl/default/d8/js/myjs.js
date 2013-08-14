$(function(){
	/*$(".txtSty").focus(function(){
		$(this).addClass("txtSty2");
	});
	$(".txtSty").blur(function(){
		$(this).removeClass("txtSty2");
	});
	$("#dgimg").toggle(
		function(){
			$(parent.document).find("#frame-body").attr("cols","0,7,*")
			$(this).attr("src","images/drag2.gif");
		},
		function(){
			$(parent.document).find("#frame-body").attr("cols","186,7,*")
			$(this).attr("src","images/drag1.gif");
		}
	);

	$(".li1").toggle(
		function(){
			$(this).next().slideDown("slow");
		},
		function(){
			$(this).next().slideUp("slow");
		}
	);
*/
	$(".tabCon:first").show();

	$(".mybtn>li").each(function(i){
		$(this).click(function(){
			$(".tabCon:visible").hide();
			$(".se").attr("class","ss");
			$(this).attr("class","se");
			$(".tabCon").eq(i).show();
		});
	});
	
	$(".yhtab:first").show();

	$(".yhzxbtn>li").each(function(i){
		$(this).click(function(){
			$(".yhtab:visible").hide();
			$(".mo").attr("class","mt");
			$(this).attr("class","mo");
			$(".yhtab").eq(i).show();
		});
	});
	
	$(".jbzl:first").show();

	$(".zlws>li").each(function(i){
		$(this).click(function(){
			$(".jbzl:visible").hide();
			$(".zl").attr("class","ws");
			$(this).attr("class","zl");
			$(".jbzl").eq(i).show();
		});
	});
	
	
	$(".tx_sc:first").show();

	$(".tx_sz>li").each(function(i){
		$(this).click(function(){
			$(".tx_sc:visible").hide();
			$(".tt").attr("class","mm");
			$(this).attr("class","tt");
			$(".tx_sc").eq(i).show();
		});
	});
	
	
	$(".yhzh_aq:first").show();

	$(".mima>li").each(function(i){
		$(this).click(function(){
			$(".yhzh_aq:visible").hide();
			$(".mi").attr("class","ma");
			$(this).attr("class","mi");
			$(".yhzh_aq").eq(i).show();
		});
	});
	
	
	/*$(".searForm>li:odd").addClass("oddColor");
	$(".table1>tbody>tr:odd").addClass("oddColor");
	$(".table1>tbody>tr").hover(
		function(){
			$(this).addClass("overColor");
		},
		function(){
			$(this).removeClass("overColor");
		}
	);*/
})