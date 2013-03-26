

// 提示
function message(txt) {
    dhtmlx.message.defPosition="left";
    dhtmlx.message({
		text:txt,
		lifetime:5000,
	//	type: "error" // 'error' - css class
     });
}


// 处理得到版本
function restartok(loader){
   var txt = loader.xmlDoc.responseText;
   var divOb = document.getElementById("bottom");
   divOb.innerHTML = txt;
   divOb.style.display = "block";
   divOb.ondblclick = hide;
   closeWindow(tForm.w, window.frameElement.contentDocument);
}



var tForm = new Object()
function restart(svr){
   var frame = window.frameElement;
   tForm.w = createWindow(frame.contentDocument.body, "重启中...");
   var param = "?game="+frame.userdata.game;
   param += "&svr="+frame.userdata.svr;
   param += "&gft="+svr;
   dhtmlxAjax.get(CONFIG.restart+param, restartok);
}


// 隐藏事件对象
function hide(event) {
    event.target.style.display = "none";
}

