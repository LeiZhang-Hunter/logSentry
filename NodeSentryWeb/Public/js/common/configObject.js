var configObject = {
    setUrl:"",
    selectId:"",
    editorObject :"",
    initJsonEditor:function (id,data) {
        var container = document.getElementById(id);
        this.selectId = id;
        var options = {
            mode: 'tree',
            modes: ['code', 'tree'], // allowed modes
            error: function (err) {
                alert(err.toString());
            }
        };
        var editor = new JSONEditor(container, options);
        this.editorObject = editor;
        editor.set(data);
    },

    getObject:function()
    {
        return this.editorObject;
    },
    
    set:function () {
        var json = this.editorObject.get();
        $.post(this.setUrl,{"config":json},function (response){
            $.waring(response.response);
        },"json");
    }
};