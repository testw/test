

// 提示
function message(txt) {
    dhtmlx.message.defPosition="left";
    dhtmlx.message({
		text:txt,
		lifetime:5000,
	//	type: "error" // 'error' - css class
     });
}


//清除一个上传控件的内容
function clearFile(id) {
    var up = (typeof id == "string") ? document.getElementById(id) : id;//如果传过来的参数是字符,则取id为该字符的元素,如果无此元素,则返回空
    if (typeof up != "object") return null;

    var tt = document.createElement("span"); //创建一个span元素
    tt.id = "__tt__"; //添加id,以便后面使用
    up.parentNode.insertBefore(tt, up);
    
    var tf = document.createElement("form"); //创建一个form
    tf.appendChild(up); //将上传控件追加为form的子元素

    document.getElementsByTagName("body")[0].appendChild(tf); //将form加入到body

    tf.reset(); //利用重置来清空上传控件内容

    tt.parentNode.insertBefore(up, tt); //所上传控件放回原来的位置

    tt.parentNode.removeChild(tt); //除上面创建的这个span
    tt = null; 
    
    tf.parentNode.removeChild(tf); //移除上面临时创建的form
}


//检查上传的文件类型是否合法
function validType(ftype, filename) {
    if (filename.length == 0) {
        alert("请选择要上传的文件\n" );
        return false;
    }
    var tmp1 = ftype.split(",");
    var tmp2 = filename.split(".");
    var efile = tmp2[tmp2.length-1];
    for (i in tmp1) {
        if (efile == tmp1[i])
            return true;        
    }
    alert("只能上传以" + ftype + "为后缀的文件！\n" + filename);
    return false;
}

// 提交表单(上传文件)
var tForm = new Object();
function onSubmit(form) {
    var filename = form.filename;
    var selectObj = form.ver;
    var prompt = form.nextElementSibling;
    var frame = window.frameElement;
    if (validType(form.ftype.value, filename.value)) {
        form.svr.value = frame.userdata.svr;
        form.game.value = frame.userdata.game;
        message(frame.userdata.sname + ":" + form.filename.value);

        tForm.f = form;
        tForm.t = form.target;
        tForm.w = createWindow(frame.contentDocument.body, "文件上传中。。。", true);
        
        form.action = CONFIG.upload;
        form.target = "dummy_frame";
        form.submit();
    }
    return false;
}

// 处理得到版本
function processGetVer(loader){
    var verArray = loader.xmlDoc.responseText.split("\n");
    for (j=0;j<2;j++) {
        var form = document.forms[j];
        for(i=0;i<verArray.length;i++){
            if(verArray[i].length <= 1)
               continue;
            form.ver.options.add(new Option(verArray[i].split("/")[1], verArray[i]+"/"));
        }
         form.ver.options.selectedIndex = 0;
       form.ver.parentElement.style.display = "block";
    }
	closeWindow(tForm.w, window.frameElement.contentDocument);
}

// 处理svnupdate(更新服务器版本目录的svn)返回值
function processSvnUpdate(loader){
    message("svn更新完成：\n" + loader.xmlDoc.responseText)
}

//页面加载是调用，生成左侧树结构
function load(event) {
    var frame = window.frameElement;
    if (frame.userdata.isbk == "true") {
		tForm.w = createWindow(frame.contentDocument.body, "初始化过程中...");
        dhtmlxAjax.get(CONFIG.svnupdate+"?game="+frame.userdata.game+"&svr="+frame.userdata.svr, processSvnUpdate);
        dhtmlxAjax.get(CONFIG.getver+"?game="+frame.userdata.game+"&svr="+frame.userdata.svr, processGetVer);
    }
    var field = document.getElementById("field");
    field.firstElementChild.innerHTML = frame.userdata.sname; // 修改区域名字
}

// 隐藏事件对象
function hide(event) {
    event.target.style.display = "none";
}


// 表单提交成功响应函数
function submitCallback(event) {
    if(!tForm.f)
       return;

    var text = event.target.contentDocument.body.innerHTML;
    var divOb = tForm.f.nextElementSibling;
    divOb.innerHTML = text;
    divOb.style.display = "block";
    divOb.ondblclick = hide;
    tForm.f.reset();  // 重置form表单
    closeWindow(tForm.w, event.target.ownerDocument);
}


// 设置iframe的onload事件，用户响应表单提交成功
setTimeout(function () {
    var iframe = document.getElementById("dummy_frame");
    iframe.onload = submitCallback;
}, 1);

// 页面加载
window.onload = load;
