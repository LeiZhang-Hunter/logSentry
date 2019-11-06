$(function(){
	var liList=$("#menuList").find("li");
	// 增加一级菜单按钮
	var addMenu = $(".no_extra").clone();
	// 增加二级菜单按钮
	var addMenu2 = $(".js_addMenuBox2").clone();
	var menuFlag0=false;
	var menuFlag1=false;
	var menuFlag2=false;
	
	$(".js_openMenu").click(function(){
		$(".js_startMenuBox").hide();
		$(".menu-setting-msg").hide();
		$("#menustatus_2").show();
		$("#pubBt").show();
		$(".js_editBox").show();
		$("#orderDis").css("display","inline-block");
		$(".js_menu_name").val("菜单名称");
	});
	//文本框处于得到焦点
	$(".js_menu_name").focus();
	//增加菜单个数，最多添加3个一级菜单
	$("#menuList").delegate(".js_addL1Btn","click",function(){
		$(".menu-form-area,.tool_bar,.sort-btn-wrp").show();
		$(".js_menu_name").val("菜单名称");
		$(".global-info").text("菜单名称");
		$(".js_menu_name").focus();
		$(".js_l2TitleBox").hide();
		var clone = "<li class='jsMenu pre-menu-item grid-item jslevel1 size1of3 current' id='maggie_menu_0'><a href='javascript:void(0);' class='pre-menu-link js_show' draggable='false'><i style='display: none;' class='icon-menu-dot js_icon_menu_dot dn'></i><i class='icon20-common sort-gray'></i><span class='js_l1Title'>菜单名称</span></a><div class='sub-pre-menu-box js_l2TitleBox'><ul class='sub-pre-menu-list list-unstyled'><li class='js_addMenuBox'><a href='javascript:void(0);' class='jsSubView js_addL2Btn' title='最多添加5个子菜单' draggable='false'><span class='sub-pre-menu-inner js_sub_pre_menu_inner'><i class='icon14-menu-add'></i></span></a></li></ul><i class='arrow arrow_out'></i><i class='arrow arrow_in'></i></div></li>"
		if($(".jsMenu").length>0){
			$(".pre-menu-item").removeClass("size1of2 current").addClass("size1of3");
			$("#orderBt").show();
			$("#orderDis").hide();
			if($(".jsMenu").length!=2){
				$(".no_extra").before(clone);
				$(".jsMenu:last").attr("id","maggie_menu_1");
			}else{
				$(".no_extra").before(clone);
				$(".jsMenu:last").attr("id","maggie_menu_2");
				$(".no_extra").remove();
				$(this).unbind("click");
			}
		}else{
			$(".no_extra").before(clone);
			$(".pre-menu-item").removeClass("size1of1 size1of3").addClass("size1of2");
		}
	});
	// 点击一级菜单事件
	// delegate(selector,[type],[data],fn) 指定的元素（属于被选元素的子元素）添加一个或多个事件处理程序，并规定当这些事件发生时运行的函数
	$("#menuList").delegate(".js_show","click",function(){
		$(".menu-form-area").show();
		$(".js_l2TitleBox").hide();
		$("#menuList").find("li").removeClass("current");
		$(this).parent("li").addClass("current").siblings().removeClass("current");
		$(this).next().show();
		strTest = $(this).find(".js_l1Title").text();
		$(".global-info").text(strTest);
		$(".js_menu_name").val(strTest);
		if($(this).parent("li").find("li").hasClass('jslevel2')){
			$("#menuRadio").hide();
			$("#menuCon").hide();
		}else{
			$("#menuRadio").show();
			$("#menuCon").show();
		}
		//是否处于排序状态
		if($("#finishBt").css("display")!="none"){
			if($(this).next().find(".jslevel2").length==0){
				$(this).next().hide();
			}
		}
	});
	// 新增二级菜单
	$("#menuList").delegate(".js_addL2Btn","click",function(){
		var menuCurrent = $(this).parents(".jslevel1").attr("id");
		var flag=$(this).parents(".jslevel1").hasClass("menuFlag");
		var thm = "js_addL2Btn";
		if(!flag){
			$(this).parents("ul").find("li").removeClass("current");
			$(".js_menu_name").val("子菜单名称").focus();
			$(".global-info").text("子菜单名称");
			var nava = "<li class='jslevel2 current'><a href='javascript:void(0);' class='jsSubView' draggable='false'><span class='sub-pre-menu-inner js_sub_pre_menu_inner'><i class='icon20-common sort-gray'></i><span class='js_l2Title'>子菜单名称</span></span></a></li>"
			if($(this).parent("li").siblings(".jslevel2").length!=4){
				$(this).parents("li").find(".js_icon_menu_dot").css("display","inline-block");
				$(this).parents("li").removeClass("current");
				$(this).parent("li").before(nava);
			}else{
				$(this).parents("li").find(".js_icon_menu_dot").css("display","inline-block");
				$(this).parents("li").removeClass("current");
				$(this).parent("li").before(nava);
				$(this).parent("li").remove();
				$(this).unbind("click");
			}
			if($(".jslevel2").length>=2){
				$("#orderBt").show();
				$("#orderDis").hide();
			}
		}
		else{
			$("body").addClass("modal-open");
			$("#flagModal").removeClass("hide");
			$("#flagModal").find(".modal").fadeIn();
			flag=false;
			$(".cancel_btn").click(function(){
				cancelBtn();
			});
			$(".submit_btn").click(function(){
				$(".mass-content").addClass("hide");
				$(".mass-content").next().show();
				cancelBtn();
				// 触发自定义事件
				$('.js_addL2Btn').trigger("click");
				$("#"+menuCurrent).removeClass("menuFlag");
			});
		}
	});
	function cancelBtn(){
		$("body").removeClass("modal-open");
		$("#flagModal").addClass("hide");
		$("#flagModal").find(".modal").fadeOut();
	};
	// 点击二级菜单事件
	// delegate(selector,[type],[data],fn) 指定的元素（属于被选元素的子元素）添加一个或多个事件处理程序，并规定当这些事件发生时运行的函数
	$("#menuList").delegate(".jslevel2","click",function(){
		$("#menuList").find("li").removeClass("current");
		$(this).addClass("current").siblings().removeClass("current");
		strTest = $(this).find(".js_l2Title").text();
		$(".global-info").text(strTest);
		$(".js_menu_name").val(strTest);
		$("#menuRadio").show();
	});
	// 输入框失去焦点事件
	$(".js_menu_name").blur(function(element){
		// 获得当前选中的li标签
		var liCurrent=$("li").filter(".current");
		var strText =$(this).val();
		if(liCurrent.hasClass('jslevel1')){
			liCurrent.find("span.js_l1Title").text(strText);
		}else{
			liCurrent.find("span.js_l2Title").text(strText);
		}
		$(".global-info").text(strText);
	});
	// 删除菜单
	$("#jsDelBt").bind("click",function(){
		// 获得当前被选中的元素
		var liCurrent=$("li").filter(".current");
		if(liCurrent.hasClass('jslevel1')){
			var menuName = liCurrent.find(".js_l1Title").text();
			$("#delMenu").find("#menuName").text(menuName);
			//一级菜单的个数
			var len1=$("#menuList").find(".pre-menu-item").length;
			// 判断添加菜单是否存在
			if($(".no_extra").length > 0){
				if(len1==2){
					$("#menuList").find(".pre-menu-item").addClass("size1of1").removeClass("size1of2");
					$(".tool_bar,.sort-btn-wrp").hide();
				}else if(len1==3){
					$("#menuList").find(".pre-menu-item").addClass("size1of2").removeClass("size1of3");
					$("#orderBt").hide();
					$("#orderDis").show();
				}
			}else{
				addMenu.addClass("size1of3").removeClass("size1of2");
				$("#menuList").append(addMenu);
			}
			liCurrent.remove();
		}else{
			var menuName = liCurrent.find(".js_l2Title").text();
			//当前ul下的子菜单的个数
			len2=liCurrent.siblings(".jslevel2").length+1;
			//是否存在子菜单的新增菜单按钮
			len3=liCurrent.siblings(".js_addMenuBox2").length;
			console.log($(".js_addL2Btn").length);
			$("#delMenu").find("#menuName").text(menuName);
			if(len2<=1){
				liCurrent.parents("li").find(".js_icon_menu_dot").hide();
				if(liCurrent.siblings(".js_addMenuBox").length==0){
					alert(liCurrent.siblings(".js_addMenuBox").length);
					liCurrent.after(addMenu2);
				}
			}
			liCurrent.remove();
			$(".js_l2TitleBox").hide();
		}
		$("#delMenu").hide();
		$(".modal-backdrop").remove();
		$(".menu-form-area").hide();
	});
	// 菜单排序点击事件
	$("#orderBt").bind("click",function(){
		$(this).hide();
		$(".tool_bar,.js_icon_menu_dot").hide();
		$("#js_rightBox").hide();
		$("#menuList").addClass("sorting");
		$("#js_none").show().text("请通过拖拽左边的菜单进行排序");
		var len=$(".jslevel1").length;
		$(".js_addMenuBox").hide();
		$("#finishBt").show();
		if(len==2){
			$(".jslevel1").addClass("size1of2").removeClass("size1of3");
			$(".no_extra").hide();
		}else if(len==1){
			$(".jslevel1").addClass("size1of1").removeClass("size1of2");
			$(".no_extra").hide();
		}
		//是否有二级菜单
		if($("jslevel2").length==0){
			$(".js_l2TitleBox").hide();
		}
		$("#menuList").addClass("sortable");
		//排序
		if($("#menuList").hasClass("sortable")){
			alert("是否有："+$("#menuList").hasClass("sortable"))
			if($("#finishBt").css("display")!="none"){
				$(".sortable").sortable();
				$(".sortable").disableSelection();
			}
		}else{
			alert("none")
		}
	});
	// 菜单排序完成点击事件
	$("#finishBt").bind("click",function(){
		$(".sortable").sortable('destroy');
		$("#menuList").removeClass("sortable");
		$("#orderBt").show();
		$(this).hide();
		$(".tool_bar,.js_addMenuBox").show();
		$("#js_rightBox").show();
		$("#menuList").removeClass("sorting");
		var len=$(".jslevel1").length;
		$("#js_none").hide();
		if(len==2){
			$(".jslevel1").addClass("size1of3").removeClass("size1of2");
			$(".no_extra,.js_addMenuBox").show();
		}else if(len==1){
			$(".jslevel1").addClass("size1of2").removeClass("size1of1");
			$(".no_extra,.js_addMenuBox").show();
		}
		//是否有二级菜单
		if($("jslevel2").length>0){
			$("jslevel2").parents("li").find(".js_icon_menu_dot").show();
		}
		// 当前选中菜单显示二级菜单
		if($("#menuList").find("li").hasClass("current")){
			$(".current").children(".js_l2TitleBox").show();
		}
	});
	// 单选按钮选中事件
	$(".js_radio_sendMsg").each(function(index,element){
		$(this).on("click",function(){
			$(".jsMain").eq(index).show().siblings().hide();
		})
	});
	//选择素材
	$(".add-gray-wrp").bind("click",function(){
		$(".modal").find(".thumbnail").each(function(){
			$(this).click(function(){
				console.log("选中了");
				var currentTab = $("#edit").find(".tab-pane").filter(".active");
				currentTab.children(".mass-content").removeClass("hide");
				currentTab.children(".tab-cont-cover").hide();
				//当前选中菜单
				$("#menuList > li").filter(".current").addClass("menuFlag");
				$(".modal").hide();
				$(".modal-backdrop").remove();
			})
		})
		$(".img-grid > li").each(function(){
			$(this).click(function(){
				console.log("选中了");
				var currentTab = $("#edit").find(".tab-pane").filter(".active");
				currentTab.children(".mass-content").removeClass("hide");
				currentTab.children(".tab-cont-cover").hide();
				//当前选中菜单
				$("#menuList > li").filter(".current").addClass("menuFlag");
				$(".modal").hide();
				$(".modal-backdrop").remove();
			})
		})
	});
	// 删除素材、图片、视频、音频
	$(".mass_delete").bind("click",function(){
		$(this).parents(".mass-content").addClass("hide");
		$(this).parents(".mass-content").next().show();
		$("#menuList > li").filter(".current").remove("menuFlag");
	});
	// 预览
	$("#viewBt").bind("click",function(){
		$(".preview-box").removeClass("hide");
		var viewMenu=$("#menuList").html()
		$("#viewList").append(viewMenu);
		$("#viewList > li").removeClass("jslevel1 current").addClass("jsViewLi");
		$("#viewList > li > a").removeClass("js_show").addClass("jsView");
		$("#viewList > li").find(".sub-pre-menu-box").removeClass("js_l2TitleBox").addClass("jsSubViewDiv")
		$("#viewList").find(".sub-pre-menu-list").removeAttr("id");
		$("#viewList").find(".no_extra,.js_addMenuBox").remove();
		$("#viewList").find(".icon20-common").remove();
		$("#viewList").find(".sub-pre-menu-list > li").removeAttr("class");
		$(".jsSubViewDiv").hide();
		var len1 = $("#menuList").find(".jslevel1").length;
		
		if(len1==1){
			$(".jsViewLi").removeClass("size1of2").addClass("size1of1");
		}else if(len1==2){
			$(".jsViewLi").removeClass("size1of3").addClass("size1of2");
		}
	});
	// 点击预览菜单
	$("#viewList").delegate(".jsViewLi","click",function(){
		var len2 = $(this).find("li").length;
		console.log("子菜单个数："+len2);
		if(len2>0){
			$(this).children(".jsSubViewDiv").toggle();
			$(this).siblings().children(".jsSubViewDiv").hide();
		}
	});
	// 退出预览
	$("#viewClose").bind("click",function(){
		$(".preview-box").addClass("hide");
		$("#viewList > li").remove();
	});
	// 确认发布
	$("#save_release").click(function(){
		$(this).parents(".modal-content").hide();
		$(".release-success").show();
	})
})
