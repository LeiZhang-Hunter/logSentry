$(function(){

    //停用/启用 按钮事件
    // $(".alert-sure").click(function(){
    //     if($(".alert-btn").text() == "停用"){
    //         $(".alert-btn").text("开启").removeClass("btn-danger").addClass("btn-success");
    //         $("#stopuse,.modal-backdrop,.reply").hide();
    //         $(".alert").removeClass("alert-success").addClass("alert-info");
    //         $(".alert-wrap-icon").removeClass("icon-unlock").addClass("icon-lock");
    //         $(".alert-wrap-t1").text("未开启自动回复设置");
    //     }else{
    //         $("#stopuse,.modal-backdrop,.reply").show();
    //         $(".alert-btn").text("停用").removeClass("btn-success").addClass("btn-danger");
    //         $(".alert").removeClass("alert-default").addClass("alert-success");
    //         $(".alert-wrap-icon").removeClass("icon-lock").addClass("icon-unlock");
    //         $(".alert-wrap-t1").text("已开启自动回复设置");
    //     }
    // })

    //添加规则按钮事件
    $(".addrules-btn").click(function(){
        $(".rules-list").eq(0).toggle();
    })

    //被添加自动回复等3个按钮的切换选中
    $(".reply-head > a").each(function(index,element){
		$(this).click(function(){
			$(".tab-tog").eq(index).show().siblings().hide();
            $(this).addClass("active").siblings().removeClass("active");
		});
	});


    //关键词新规则部分点击隐藏
    $(".newrules-head").click(function(){
        $(this).siblings(".open,.closed").toggle();
        // $(".open,.closed").toggle();
    });

    //关键字部分点击事件
    $(".unallmatch").click(function(){
        if($(this).text() == "未全匹配"){
            $(this).text("已全匹配");
        }else{
            $(this).text("未全匹配");
        }
    })


    // $(".newrules").click(function(){
    //     $(".needhide").toggle();
    //     // $(".ruleskeyword").append('<p class="pull-left">添加的关键字名</p>');
    //     // $(".rulesreply").html("<p> 0 ( 0 条文字 ，0 条图片 ) </p>");
    //     $(".rulesreply-hide,.ruleskeyword-hide").toggle();
    //     $(".addrules-con > ul").css("border-bottom","1px solid #e7e7eb")
    // });



    // $(".addkeywordsli").click(function(){
    //     // console.log($(".keywords-text").val());
    //         $(".ruleskeyword").after('<li><div class="reply-keywords"></div></li>')
    //         $(".ruleskeyword").next().find(".reply-keywords").text($(".keywords-text").val());
    //         $(".keywords-text").text("");
    // });

    //选择素材
	$(".add-gray-wrp").bind("click",function(){
		$(".img-grid > li").each(function(){
			$(this).click(function(){
				console.log("选中了");
				var currentTab = $(".tianjia").find(".tab-pane").filter(".active");
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
		// $("#menuList > li").filter(".current").remove("menuFlag");
	});

})
