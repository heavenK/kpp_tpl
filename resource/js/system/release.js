/*
 *任务发布公有js 
*/

$(function(){
	$("#qq").click(function(){
		$("#ct_qq").toggle();
	})
	$("#msn").click(function(){
		$("#ct_msn").toggle();
	})
	
	$(".show_more a").toggle(function(){
		$(this).children('span').removeClass('arrow_b').addClass('arrow_t');
		$("#task_dec").height('auto');
	},function(){
		$(this).children('span').removeClass('arrow_t').addClass('arrow_b');
		$("#task_dec").height('6em');
	});
//	var s = setInterval(function(){
//		
//		if($("#point").val()!=''){
//			
//		}
//	},2000);
	
	 $("input[item_type='use'][id!='payitem_map']").each(function(){
		$("#remain_num_"+$(this).attr("item_code")).text(parseInt($("#remain_num_"+$(this).attr("item_code")).text())-$(this).val());
	})
	
//	contact();
//	$(":radio[name='contact_type']").click(function(){$(this).attr("checked","checked");contact()});
	
	$("#tar_content").blur(function(){
		contentCheck('tar_content',L.t_require,50,1000);
	})
	$(".agreement_link").toggle(function(){
		$(".agreement_part").show();
	},function(){
		$(".agreement_part").hide();
	});
	
	 $(".action_show").toggle(function(){
         $('#help_center').removeClass('hidden');
		   $(this).children('span').removeClass('arrow_b').addClass('arrow_t');
      },function(){
			$('#help_center').addClass('hidden');
			$(this).children('span').removeClass('arrow_t').addClass('arrow_b');
	});
})
/**
 * 联系方式清空
 */
//function contact(){
//	var contact_type = parseInt($(":radio[name='contact_type']:checked").val())+0;
//		if(contact_type=='1'){
//			$(".lit_form input:[type='text']").removeAttr("ignore").removeAttr("disabled").val('');
//		}else{
//			$(".lit_form input:[type='text']").each(function(){
//				$(this).attr("disabled","disabled").attr("ignore","true").val($(this).attr("ext"));
//			})
//		}
//}
/**
 * 获取相应预算范围内的最大天数
 * @param task_cash
 */
function getMaxDday(task_cash){
	if(task_cash){
		$.getJSON(basic_url,{ajax:'getmaxday',task_cash:task_cash},function(json){
			$(".lit_form .pad10 span:last-child").removeClass().text('');
			if(json.status==1){ 	
				 //$("#txt_task_day").attr("limit","required:true;type:date;than:min;less:"+json.msg).val(json.msg);
				 $("#max").val(json.msg); 
				 var min_day = $("#txt_task_day").attr("min_day");
				 title=L.t_allow_min_day+min_day+L.t_allow_max_day+json.data;
				 $("#txt_task_day").attr("title",title); 
				 $("#txt_task_day").attr("max",json.msg); 
				 $("#txt_task_day").attr("msg",title);
			}else
				return false;
			})
	}
}



//显示隐藏使用天数的输入框
function show_payitem_num(obj,item_code){
	
	var item_code = item_code;
	var checked = $(obj).attr("checked");  
	if(checked ==true){ 
		if(item_code=='map'){
			$("#set_map").show(); 
			add_payitem($("#item_map"),'add',1);  
		}else{
			$("#span_"+item_code).show();  
		}
	}else{ 	
		if(item_code=='map'){
			add_payitem($("#item_map"),'del',1);  
			$("#set_map").hide(); 
		}else{
			del_payitem(item_code);//删除增值服务
			$("#span_"+item_code).hide(); 
			$("#payitem_"+item_code).val(""); 
		} 
	} 
}


//编辑增值服务
function edit_payitem(item_code){
	var item_code = item_code;
	if($("#payitem_"+item_code).val()){
		var payitem_num = parseInt($("#payitem_"+item_code).val())+0;
	}else{
		var payitem_num = 0;
	}
	
	var item_cash = parseFloat($("#item_cash_"+item_code).val());
	var total_cash = parseInt( $("#ago_total").val()); 
//	alert(payitem_num);return false;
	add_payitem($("#payitem_"+item_code),'add',payitem_num); 
}

//删除增值服务
function del_payitem(item_code){
	var item_code = item_code;
	var payitem_num = parseInt($("#payitem_"+item_code).val()); 
	add_payitem($("#checkbox_"+item_code),'del',payitem_num);  
}

/**
 * 检查任务周期
 * @returns {Boolean}
 */
function checkDay(){
	var max_day = parseInt($("#txt_task_day").attr("max"))+0;
	var day     = parseInt($("#txt_task_day").val())      +0;
	
	if(day>max_day){
		$("#span_task_day").html("<span>"+L.t_amount_allowable_period+max_day+L.day+"</span>");
		return false;
	}else
		return true;
}
/**
 * 检测是否同意协议
 */
function checkAgreement(){
	if($("#agreement").attr("checked")==false){
		showDialog(L.t_publishing_agreement,"alert",L.operate_notice);return false;
	}else return true;
}
function stepCheck(model_id){
	//alert(1);return false;
	var i 	 = checkForm(document.getElementById('frm_'+r_step));
	var pass = false;
	switch(r_step){
		case "step1":
			if(checkDay()){
				if(i){
					pass=true;
				}
			}
			break;
		case "step2": 
			if(i){ 
				if(contentCheck('tar_content',L.t_require,50,1000,0,'',editor)&&checkAgreement()){
					pass = true;
				}
				if(model_id==8&&checkAgreement()){
					pass = true;
				}
			}
			break;
		case "step3":
		if(checkDay()){
				if(i){
					pass=true;
				}
			}
			if($("input[id=payitem_map][item_type=buy]").val()!=0&&$("#payitem_map").attr("item_type")=='buy'&&$.trim($("#point").val())==''){
				showDialog('您没有设置地图','alert','操作提示');return false;
			}else{
				if(i){
					//alert(i);return false;
					$("#frm_"+r_step).submit();
					$("button[name='is_submit']").unbind("click").addClass('disabled');
				}else{
					return false;
				}				
			}
			break;
		case "step4":
			
			break;
	}
	if(pass==true){
	
		check_pub_priv();
	}
}
/**
 * 发布权限检测
 * @returns {Boolean}
 */
function check_pub_priv(){
	
	$.getJSON(basic_url,{ajax:"check_priv"},function(json){
		if(json.status=='1'){
			$("#frm_"+r_step).submit();
		}else{
			
			showDialog(json.data,"alert",json.msg);return false;
		}
	})
}
/**
 * 增值项添加
 * @param obj 当前对象
 * @param action当前动作  add增加/del删除
 */
function add_payitem(obj,action,item_num){
	//alert(action);
	//alert(item_num);
	//alert(obj,action,item_num);
	var item_id = parseInt($(obj).attr('item_id'))+0;
	var item_cash = parseFloat($(obj).attr('item_cash')*item_num);
	var item_name = $.trim($(obj).val());
	var item_code = $.trim($(obj).attr("item_code"));
	var item_type = $(obj).attr("item_type");
	var exist_num = $(obj).attr("exist_num");
	var total_cash = parseFloat($("#total").text().toString());//总金
	//alert(exist_num);
    //return false;
	if(item_code=='map'&&item_type=='buy'){
		if(item_num){
			$("#buy_map").show();
		}else{
			$("#buy_map").hide();
			$("#point").val('');
			$("#hdn_province").val('');
		}
	}
	//alert(item_type);
	//alert(item_num);return false;
	if(item_type=='use'){
		//alert(exist_num);
			var remain = parseInt(exist_num)-item_num;
			$("#remain_num_"+item_code).text(remain);
	}
//	if(item_num=='use'&&item_code=='map'&&$("#point").val()){
//		var remain = parseInt($("#remain_num_"+item_code).text())-item_num;
//		$("#remain_num_"+item_code).text(remain);
//	}
	
	switch(action){
		case "add":
			$.post(basic_url,{ajax:"save_payitem",item_id:item_id,item_name:item_name,item_cash:item_cash,item_code:item_code,item_num:item_num,item_type:item_type},function(json){
				$("#total").html(json.msg);
				$("#item_count").html(json.data.item_count);
				$("#item_cash").html(json.data.item_cash);
			},'json')
			break;
		case "del":
			$.post(basic_url,{ajax:"rm_payitem",item_id:item_id},function(json){
					$("#total").html(json.msg);
			},'json')
			break;
	}
}
/**
 * 上传完成后的页面响应
 * @param json json数据
 */
function uploadResponse(json){
	if($("#"+json.fid).length<1){//判断是否已有同样的li、
		var file_ids = $("#file_ids").val();
		if(file_ids){
			$("#file_ids").val(file_ids+','+json.fid)
		}else{	
			$("#file_ids").val(json.fid);
		}
	}
   
}