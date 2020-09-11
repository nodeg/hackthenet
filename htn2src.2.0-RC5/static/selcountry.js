var maps=new Array("afrika","amerika","europa","australien","asien");
var xx,yy;

function showMap(map) {
if(map=="world") var w=true; else var w=false;
getLay("weltkarte").style.display=(w==true?"block":"none");
for(i=0;i<maps.length;i++) getLay("karte_"+maps[i]).style.display="none";
if(!w) getLay("karte_"+map).style.display="block";
}


function control(e) {
if(document.getElementById && !document.all) xx=e.clientX;
if(document.getElementById && !document.all) yy=e.clientY;
if(document.all || document.layers) {
xx=(document.layers) ? e.pageX : document.body.scrollLeft + event.clientX;
yy=(document.layers) ? e.pageY : document.body.scrollTop + event.clientY;
}
}

function cinfo(text) {
var lay=getLay("tip");
if(text!=0) {
lay.innerHTML=text;
lay.style.left=xx+20; lay.style.top=yy;
lay.style.display="block";
} else { lay.style.display="none"; }
setTimeout("xstat('"+(text==0?'':text)+"')",1);
}

function xstat(text) { status=text;return true;return true; }

document.onmousemove=control;