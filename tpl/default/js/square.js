

	$(function(){
		
		$(".togg_a").focus(function(){
			//alert($(this).attr('rel'));
			 if(this.value==$(this).attr('rel')){
				 this.value = ''; 
			 }
        }).blur(function(){
        	if(this.value == '' ){
        		this.value = $(this).attr('rel');
        		$("#sub_button").attr("disabled",true);
        	}else{
        		if($("#tar_content").text()&&$("#tar_content").text()!='说出你的需求   >    让众多威客来帮你   >   讨论和评价'&&$("#tar_content").text()!='发布免费服务   >    让众多威客来购买   >   讨论和评价'){
        			$("#sub_button").removeAttr("disabled");
        		}
        	}
                
        })
        	$(".togg_b").focus(function(){
        		 if(this.value==$(this).attr('rel')){
    				 this.value = ''; 
    			 }
        }).blur(function(){
        	if(this.value == '' ){
        		this.value = $(this).attr('rel');
        		$("#sub_button").attr("disabled",true);
        	}else{
        		if($("#txt_title").val()&&$("#txt_title").val()!='需求设计标志、取名、开发网站、写策划案...'&&$("#txt_title").val()!='免费服务出售...'){
        			$("#sub_button").removeAttr("disabled");
        		}
        	}
        })
		//搜索选项
	$("#pub_select a.selected").click(function(){
		$(this).nextAll("a").removeClass("hidden");
		
	});
    
	$("#pub_select a").not(".selected").click(function(){
		$("#pub_select .selected").attr("rel",$(this).attr("rel")).children("span").html($(this).html()).end().nextAll("a").addClass("hidden");
		if($(this).attr("rel")=='free_task'){
			$("#txt_title").val("需求设计标志、取名、开发网站、写策划案...");
			$("#txt_title").attr("rel",'需求设计标志、取名、开发网站、写策划案...');
			$("#tar_content").val("说出你的需求   >    让众多威客来帮你   >   讨论和评价");
			$("#tar_content").attr("rel",'说出你的需求   >    让众多威客来帮你   >   讨论和评价');
		}else if($(this).attr("rel")=='free_service'){
			$("#txt_title").val("免费服务出售...");
			$("#txt_title").attr("rel",'免费服务出售...');
			$("#tar_content").val("发布免费服务   >    让众多威客来购买   >   讨论和评价");
			$("#tar_content").attr("rel",'发布免费服务   >    让众多威客来购买   >   讨论和评价');
		}
	})
	}) 
/**
 * 监听事件
 * @param json json数据
 */
	function checkTitleLen(){
		var t_obj = $("#txt_title");
		var num1 = t_obj.val().length;
		var content_obj = $("#tar_content");
		var num2 = content_obj.text().length;
		if(num1>0&&num2>0&&t_obj.val()!='需求设计标志、取名、开发网站、写策划案...'&&t_obj.val()!='免费服务/商品出售...'&&content_obj.val()!='说出你的需求   >    让众多威客来帮你   >   讨论和评价'&&content_obj.val!='发布免费服务/商品   >    让众多威客来购买   >   讨论和评价'){
			$("#sub_button").removeAttr("disabled");
		}
		//alert(num);
	}
	/**
	 * 表单提交
	 * @param json json数据
	 */
	function freeSub(){
		if(check_user_login()){
			var i       = checkForm(document.getElementById('free_form'));
			if(i){
				var shtml  = contentCheck('tar_content','免费需求',5,100,1,'upload_tip');
				if(shtml){
					var type      = $("#pub_select .selected").attr("rel");
					$("#pub_type").val(type);
						if($("#txt_title").val()=='需求设计标志、取名、开发网站、写策划案...'||$("#txt_title").val()=='免费服务/商品出售...'){
							$("#txt_title").val('');
							$("#txt_title").focus();
							return false;
						}else if($("#tar_content").val()=='说出你的需求   >    让众多威客来帮你   >   讨论和评价'||$("#tar_content").val()=='发布免费服务/商品   >    让众多威客来购买   >   讨论和评价'){
							$("#tar_content").val('');
							$("#tar_content").focus();
							return false;
						}else{
							formSub('free_form','form',false);return false;
						}
				}
			}
		}
	}
	$('a#add_file,a#add_pic').click(function(){
		var po_box = $(this).parent('li');
		$(this).addClass('selected').siblings('.add_des').show();
		po_box.css('zIndex','3').siblings().css('zIndex','2')
		po_box.siblings().children('.add_des').hide().siblings('a').removeClass('selected');
	});

	$("body").click(function(){
		$('.core_footer ul li a').removeClass('selected');
		$('.core_footer ul li .add_des').hide();
	});
	$(".core_footer ul li").click(function (e) {
		e.stopPropagation();
	});
	
	/**
 * 上传完成后的页面响应
 * @param json json数据
 */
function uploadResponse(json){
		if(json.msg){
			att_uploadResponse(json);
			return false;
		}else{
			var val = $.trim($("#file_ids").val(),',');
				val+=val?',':'',
				$("#file_ids").val(val+json.fid);
		}
}
//附件json相应
function att_uploadResponse(json){
	var val = $.trim($("#file_path_2").val(),',');
		val+=val?',':'',
	$("#file_path_2").val(val+json.fid);
}
var timer = setInterval(function(){
	//alert(last_id);
	var last_id = $("#last_id").val();
	var url = "index.php?do=square&view=index&t="+t+"&op=get_data&last_id="+last_id;
    $.post(url,{},function(json){
			if(json.data){
				//alert(json.data);
				var ids = json.data;
				
				$("#show_new").removeClass("hidden");
				$("#show_new").html("<a onclick=show_data('"+ids+"') class=block >有"+json.msg+"条新动态，点击查看</a>");
			}
         
    },"json");
},5000);
function show_data(data){
	//alert(data);
	var url = "index.php?do=ajax&view=menu&t="+t+"&ajax=load_square&ids="+data;
	  if(data){
		  //alert(data);
		  $.post(url,function(text){
			  $("#data_contain").prepend(text);
			  $("#show_new").addClass("hidden");
			  var arr=new Array();
			  arr = data.split(',');
				$("#last_id").val(arr[0]);
		  },'text')
	  }
}




