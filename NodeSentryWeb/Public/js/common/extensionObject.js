var extensionObject = {
    download:function(dom,downloadUrl){

        //dom 元素点击事件
        dom.click(function () {
            window.location.href = downloadUrl;
        });
    }
};