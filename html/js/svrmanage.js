

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
function requestok(loader){
   var txt = loader.xmlDoc.responseText;
   var divOb = document.getElementById("bottom");
   divOb.innerHTML = txt;
   divOb.style.display = "block";
   divOb.ondblclick = hide;
   closeWindow(tForm.w, window.frameElement.contentDocument);
}



var tForm = new Object()

// 重启服务器
function restart(gft, ty, name){
   var frame = window.frameElement;
   tForm.w = createWindow(frame.contentDocument.body, name +"：重启中请稍后...");
   var param = "?game="+frame.userdata.game;
   param += "&svr="+frame.userdata.svr;
   param += "&gft="+gft;
   param += "&shname="+ty;
   dhtmlxAjax.get(CONFIG.runshell+param, requestok);
}


// 关闭服务器
function closesvr(gft, ty, name){
   var frame = window.frameElement;
   tForm.w = createWindow(frame.contentDocument.body, name +"：关闭中请稍后...");
   var param = "?game="+frame.userdata.game;
   param += "&svr="+frame.userdata.svr;
   param += "&gft="+gft;
   param += "&shname="+ty;
   dhtmlxAjax.get(CONFIG.runshell+param, requestok);
}

// 执行指令
function svrcmd(gft, cmd, name, obj){
   var frame = window.frameElement;
   tForm.w = createWindow(frame.contentDocument.body, name +"："+obj.value+"-指令执行中...");
   var param = "?game="+frame.userdata.game;
   param += "&svr="+frame.userdata.svr;
   param += "&gft="+gft;
   param += "&cmd="+cmd;
   dhtmlxAjax.get(CONFIG.svrcmd+param, requestok);
}


// 隐藏事件对象
function hide(event) {
    event.target.style.display = "none";
}




// 生成表格标题头
var headname = ["服务器名称", "服务器状态","服务器端口状态", "操作"];
function genHead(table){
	var tr = document.createElement("tr");
	tr.className = "thead";
	for(var j=0; j<headname.length; j++){ //列
		var td = document.createElement("th");
		var txt = headname[j];
		tr.appendChild(td);
		td.innerHTML = "<p>"+txt+"</p>";
	}
	table.appendChild(tr);
}

// 生成玩家操作内容
function genOperation(tr, gft, name){
	var td = document.createElement("td");
	//td.width = "60%";
	td.textAlign = "left";

	// 重启
	var bt = document.createElement("input");
	bt.type = "button";
	bt.value = "重启";
	bt.onclick = function(){restart(gft, "restart", name);}
	td.appendChild(bt);

	// 关闭
	var bt = document.createElement("input");
	bt.type = "button";
	bt.value = "关闭";
	bt.onclick = function(){closesvr(gft, "stop", name);}
	td.appendChild(bt);

	// 清理首杀
	var bt = document.createElement("input");
	bt.type = "button";
	bt.value = "清理首杀";
	bt.onclick = function(){svrcmd(gft, "cleanfirstkill", name, this);}
	td.appendChild(bt);

	// 清理帮会竞赛
	var bt = document.createElement("input");
	bt.type = "button";
	bt.value = "清理帮会竞技";
	bt.onclick = function(){svrcmd(gft, "cleanunionwar", name, this);}
	td.appendChild(bt);

	// 清理限购记录
	var bt = document.createElement("input");
	bt.type = "button";
	bt.value = "清理限购记录";
	bt.onclick = function(){svrcmd(gft, "scare_buy", name, this);}
	td.appendChild(bt);


	tr.appendChild(td);
}

var img = "<p><img src=\"../../loading/28.gif\"> </img></p>";
var imgObj = document.createElement("img");
imgObj.src = "../../loading/28.gif";

function asyncGet(V, gft, ty){
	var frame = window.frameElement;
	//tForm.w = createWindow(frame.contentDocument.body, "初始化过程中...");
	var param = "?game="+frame.userdata.game;
	param += "&svr="+frame.userdata.svr;
	param += "&gft="+gft;
	param += "&ty="+ty;
	V.innerHTML = img;
	dhtmlxAjax.get(CONFIG.svrcfg+param, function (loader){
				var txt = loader.xmlDoc.responseText;
				V.innerHTML = "<p>" + txt + "</p>";
			} );
}



// 根据数据生成表格内容
var datakey = ["name", "process", "port"]; // 这个顺序需要跟headname的顺序对应上。
function genBody(table, data){
	var len = data.ty.length;
	for(var i=0; i<len; i++){ // 行
		var tr = document.createElement("tr");
		tr.className = i%2==0?"tbody1":"tbody2";
		for(var j=0; j<datakey.length; j++){ //列
			var tag = j==0?"th":"td";
			var td = document.createElement(tag);
			tr.appendChild(td);
			var txt  = data[datakey[j]][i];
			if(txt){
				td.innerHTML = "<p>" + txt + "</p>";
			}
			else{
				// 异步生成服务器状态
				asyncGet(td, data.ty[i], datakey[j]);
			}
		}
		genOperation(tr, data.ty[i], data.name[i]); // 生成具体的操作
		table.appendChild(tr);
	}
}

// 创建table
function newtable(data){
	var table = document.createElement("table");
	genHead(table);
	genBody(table, data);
	return table;
}

function newtitle(body, frame){
	var div = document.createElement("div");
	div.className = "title";
	div.innerHTML = "<span>"+frame.sname+"<span>";
	body.appendChild(div);
}


// 生成界面
function gentable(loader){
	var txt = loader.xmlDoc.responseText;
	var jsonObj = eval('(' + txt + ')');
	var divOb = document.getElementById("container");

	newtitle(divOb, window.frameElement.userdata);

	// 生成 table
	var table = newtable(jsonObj);
	divOb.appendChild(table);

	closeWindow(tForm.w, window.frameElement.contentDocument);

}


function init(){
	var frame = window.frameElement;
	tForm.w = createWindow(frame.contentDocument.body, "初始化过程中...");
	var param = "?game="+frame.userdata.game;
	param += "&svr="+frame.userdata.svr;
	dhtmlxAjax.get(CONFIG.svrcfg+param, gentable);
}


window.onload = init;
