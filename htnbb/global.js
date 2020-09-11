/* In dieser Datei wurde teilweise auf die Formatierung mit Leerzeichen verzichtet, um Traffic zu sparen */

var i; // eklich
var interval_id;

window.onload = startsynchr;
window.defaultStatus = 'HackTheNet';

function getLay(id)
{
  if(document.getElementById) return document.getElementById(id);
  if(document.all) return eval('document.all.'+id);
  return 0;
}

function fmtint(i)
{
  i=Math.round(i,0);
  return (i<10 ? '0'+i : i);
}

function synchrclock()
{
  
  var now = new Date();
  var c = new Date();
  var gone = now.getTime()-lstm.getTime();
  c.setTime(stm.getTime() + gone);
  
  if(typeof noclock == 'undefined')
  {
    setlabelcontent(fmtint(c.getHours()) + ":" + fmtint(c.getMinutes()) + ":" + fmtint(c.getSeconds()) + ' Uhr', 'clock');
  }
  
}

function setlabelcontent(html, id)
{
  var lay=getLay(id);
  
  if(lay!=0)
  {
    if(document.getElementById)
    {
      eval('document.getElementById(\''+id+'\').firstChild.nodeValue=html;');
    }
    else
    {
      lay.innerHTML=html;
    }
  }
}

function startsynchr()
{
  interval_id = setInterval('synchrclock()', 250);
  var temp=stm;
  stm=new Date();
  stm.setTime(temp * 1000);
  if(document.getElementsByTagName) BlurLinks();
}

function ask(q)
{
  return window.confirm(q);
}

function move_menu()
{
  var menu=getLay('navi');
  var mt=135;
  if(menu)
  {
  if(document.body && document.body.all)
  {
    var a=document.body.scrollTop;
    if(a<mt) a=mt;
    menu.style.top=a+'px';
  }
  else if(document.getElementById)
  {
    var yoff=window.pageYOffset;
    if(yoff<mt) yoff=mt;
    menu.style.top=yoff+'px';
  }
  }
}
if(typeof scroll_menu != 'undefined') setInterval('move_menu()',50);

var newwin;

function toolwin(url,w,h)
{
var p,l,t;

l=parseInt((screen.availWidth-w)/2);
t=parseInt((screen.availHeight-h)/2);

p='width='+w+',height='+h+',toolbar=0,menubar=0,location=0,status=1,resizable=1,scrollbars=1,left='+l+',top='+t;
newwin = window.open(url,'htnwin',p);
setTimeout('newwin.focus();',200);
}

function toggle_display(lay_id)
{
  getLay(lay_id).style.display = (getLay(lay_id).style.display == 'none' ? 'block' : 'none');
}
