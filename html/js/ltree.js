

function onLoadedFunc(loader) {
    alert(loader.xmlDoc.responseText);
    var con = document.getElementById("rightDiv");
    con.innerHTML = loader.xmlDoc.responseText;
}


// 得到某个id的用户自定义数据
function getAllUserDAta(treeObject, id){
    var node = treeObject._globalIdStorageFind(id);
    var userdata;
    if(node){
        userdata = new Object();
        var item = (node._userdatalist || "").split(",");
        for(i=0; i<item.length; i++){
            userdata[item[i]] = node.userData["t_"+item[i]];
        }
    }    
    return userdata; 
}

// 用户点击树上的item 打开右边界面
function onClickTreeItem(id) {
    var userdata =  getAllUserDAta(tree, id); 
    if(userdata && userdata.page) {
        var con = document.getElementById("content_iframe");
        con.userdata = userdata;
		var myDate = new Date();
        con.src = "content/" + userdata.page + "?" + myDate.getTime();
    }
}

// 窗口改变事件
function onresize(event){
    var con = document.getElementById("rightDiv");
    con.style.height = (window.innerHeight-10)+"px";
    var con1 = document.getElementById("leftDiv");
    con1.style.height = (window.innerHeight-10)+"px"; 
}

//页面加载是调用，生成左侧树结构
var tree
function load(event) {
    tree = new dhtmlXTreeObject("list_tree", "100%", "100%", "0");
    tree.setImagePath("image/dhtmlxTree/imgs/");
    tree.enableTreeLines(true);
    tree.enableTreeImages(false);
	var myDate = new Date();
    tree.loadJSON("data/tree4json_g.json?" + myDate.getTime());
    tree.attachEvent("onClick", onClickTreeItem);
    onresize(event);
}

window.onload = load;
window.onresize = onresize;
