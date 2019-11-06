var editor;
var jbo;
function openModal(type){
    $("body").addClass("modal-open");
    var html = "<div class='modal-backdrop fade in'></div>";
    $("body").append(html);
    $("#photo-box").show();
    $("#photo-box").find(".modal-dialog").animate({top:'0%'});
    $("#photo-box").addClass("in");
}
$(".close").click(function(){
    $("body").removeClass("modal-open");
    $("#photo-box").removeClass("in");
    $(".modal-backdrop").remove();
    $("#photo-box").fadeOut();
})
$(document).ready(function(){
	KindEditor.options.filterMode = false;   
	KindEditor.ready(function(K){
		editor = K.create('#editor_id');
		KindEditor.plugin('image', function(K){
            var self = this,name ='image';
            self.clickToolbar(name,function(){
                openModal("image");
            });
        });
		KindEditor.plugin('media', function(K){
			var self = this,name ='media';
			self.clickToolbar(name,function(){
				openModal("media");
			});
		});
		KindEditor.plugin('multiimage', function(K){
			var self = this,name ='multiimage';
			self.clickToolbar(name,function(){
				openModal("multiimage");
			});
		});
		KindEditor.plugin('insertfile', function(K){
			var self = this,name ='insertfile';
			self.clickToolbar(name,function(){
				openModal("insertfile");
			});
		});

	});
})

