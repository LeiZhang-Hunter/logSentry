var projectObject = {
    make:function (url,locationUrl) {
        $("#save").click(function(){
            let data = {};
            let username = $("#username").val();
            if(username === "")
            {
                $.waring("请输入项目要绑定的用户名");
                return false;
            }
            data.username = username;
            let client_id = $("select[name='client_id']").val();
            if(client_id === "")
            {
                $.waring("请选择项目要绑定的客户机");
                return false;
            }
            data.client_id = client_id;

            var id = $.getParam("id");
            if(id)
            {
                data.id = id;
            }

            let project_name = $("#project_name").val();
            if(project_name==="")
            {
                $.waring("请输入项目名称");
                return false;
            }
            data.project_name = project_name;

            let project_dir = $("#project_dir").val();
            if(project_dir === "")
            {
                $.waring("请输入项目路径");
                return false;
            }
            data.project_dir = project_dir;


            $.post(url,data,function(response){
                if(+response.code === 0)
                {
                    window.location.href = locationUrl;
                }else{
                    $.waring(response.response);
                    return false;
                }
            },"json");
        });
    }
};