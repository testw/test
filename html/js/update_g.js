

// 提示
function message(txt) {
    dhtmlx.message.defPosition="left";
    dhtmlx.message({
		text:txt,
		lifetime:5000,
	//	type: "error" // 'error' - css class
     });
}



// 提交表单(上传文件)
var tForm = new Object();
function onSubmit(form) {
    var selectObj = form.ver;
    var frame = window.frameElement;
    form.svr.value = frame.userdata.svr;
    form.game.value = frame.userdata.game;

    var ver = selectObj[selectObj.selectedIndex].textContent;
    var game = form.svr.value == "ver"?"策划验收服":"测试服务器";
    var sname = frame.userdata.sname;
    message(sname + ":" + ver);

    tForm.w = createWindow(frame.contentDocument.body, sname+"("+game+"["+ver+"]):更新中...");
    tForm.f = form;
    tForm.t = form.target;
    
    form.action = CONFIG.update;
    form.target = "dummy_frame";
    form.submit();

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
            var ver = verArray[i].split("/")[1];
            form.ver.options.add(new Option(ver, ver));
        }
         form.ver.options.selectedIndex = 0;
       form.ver.parentElement.style.display = "block";
    }
	closeWindow(tForm.w, window.frameElement.contentDocument);
}


//页面加载是调用，生成左侧树结构
function load(event) {
    var frame = window.frameElement;
    if (frame.userdata.isbk == "true") {
		tForm.w = createWindow(frame.contentDocument.body, "初始化过程中...");
        dhtmlxAjax.get(CONFIG.getver+"?game="+frame.userdata.game+"&svr="+frame.userdata.svr, processGetVer);
    }
    var field = document.getElementById("field");
    field.firstElementChild.innerHTML = frame.userdata.sname;
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
