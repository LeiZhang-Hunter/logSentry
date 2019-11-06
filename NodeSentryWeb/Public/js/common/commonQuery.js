/**
 * 公共的类包
 */
(function($)
{
    $.waring = function (msg) {
        layer.msg(msg)
    };


    /**
     *绑定点击的更改状态事件,可以注册钩子函数,注意删除标签上必须有属性delete_id
     * @param deleteUrl
     * @param state
     * @param msg
     * @param hook
     */
    $.fn.bindClickChangeStateEvent = function (deleteUrl,state,msg,hook) {
        if(!deleteUrl)
        {
            alert("请输入url");
            return false;
        }

        if(!msg)
        {
            alert("请输入提示消息");
            return false;
        }
        $(this).click(function(){
            var initDom = $(this);
            layer.confirm("你确定要执行"+msg+"操作吗？",{
                btn: ['确定', '取消'] //可以无限个按钮
            },function (index) {
                var layerIndex = layer.load();
                var data_id = initDom.attr("delete_id");
                $.post(deleteUrl,{delete_id:data_id,state:state},function(response){
                    if(+response.code === 0)
                    {
                        if(hook)
                        {
                            //触发成功的钩子
                            hook(initDom,response);
                        }else{
                            window.location.reload();
                        }
                    }else{
                        $.waring(response.response);
                    }
                    layer.close(index);
                    layer.close(layerIndex);
                },'json');
            },function(index){
                layer.close(index);
            });
        });
    };

    /**
     * 接收get参数
     * @param name
     * @returns {string}
     */
    $.getParam = function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return '';
    };

})(jQuery);