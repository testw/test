
function createWindow(own, txt, timeout_flag){
    //position:absolute;left:0px; top:0px; width:100%; height:4300%;background-color:#000000;filter:alpha(Opacity=30)
    var tranDivBack = document.createElement("div");
    tranDivBack.id = "tranDivBack";
    tranDivBack.style.position = "absolute";
    tranDivBack.style.left = "0px";
    tranDivBack.style.top = "0px";
    //tranDivBack.style.zIndex = 0;
    tranDivBack.style.width = "100%"; 
    tranDivBack.style.height = "100%"; 
    tranDivBack.style.backgroundColor = "#000000";
    tranDivBack.style.opacity = 0.3;
    tranDivBack.style.filter = "alpha(Opacity=30)";


    //position:absolute;left:0px; top:0px; width:100%; height:200%; background-color:#e5edf5;
    var infoDiv = document.createElement("div");
    infoDiv.id = "infoDiv";
    infoDiv.style.left = (window.innerWidth-400)/2+"px";
    infoDiv.style.top = "300px";
    infoDiv.style.zIndex = 1;
    //infoDiv.style.width = width; 
    //infoDiv.style.height = height; 
    //infoDiv.style.border = "3px solid #0099ff";
    //infoDiv.style.backgroundColor = "#e5edf5";
    infoDiv.style.color = "#ff0000";
    infoDiv.style.fontSize = "25px";
    infoDiv.align = "center";
    infoDiv.style.position = "absolute";
    if(!txt)
       txt  = "加载中。。。";
	var img = "<img src=\"../../loading/14.gif\"> </img>";
    infoDiv.innerHTML = "<p>"+txt+"</p>" + img;
   
    //position:absolute;display:none; left:0px; top:0px;
    var tranDiv = document.createElement("div");
    tranDiv.id = "tranDiv";
    tranDiv.style.left = "0px";
    tranDiv.style.top = "0px";
    tranDiv.style.height = window.innerHeight+ "px"; 
    tranDiv.style.width = window.innerWidth+ "px"; 
    tranDiv.style.position = "absolute";

    // 添加到界面
    tranDiv.appendChild(tranDivBack);
    tranDiv.appendChild(infoDiv);
    own.appendChild(tranDiv);

    //setTimeout(function(){closeWindow("tranDiv")}, 3000);
    if(timeout_flag){
      refreshTxt(own, tranDiv, infoDiv, infoDiv.innerHTML, 15);
    }
    return tranDiv.id;
} 

function refreshTxt(own, obj, infoDiv, txt, times){
    if (times < 0) {
        if (document.getElementById(obj.id) == obj) {
            own.removeChild(obj); 
            alert('执行超时，请重试');
        };
        return;
    };
    setTimeout(function(){ refreshTxt(own, obj, infoDiv, txt, times-1) }, 1000);
    infoDiv.innerHTML = "超时："+times+txt;
}



function closeWindow(id, doc){ 
    var tt = doc.getElementById(id);
    //tt.style.display = "none";
    if (tt) {
        tt.parentNode.removeChild(tt); 
        tt = null; 
        return true;
    };
    return false;
}


