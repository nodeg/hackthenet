<?php

define('IN_HTN',1);
include 'gres.php';
include 'layout.php';

$url=$_SERVER['PHP_SELF'];
$url=substr($url,strlen($url)-strpos(strrev($url),'/'));
$url=trim(strrev(strstr(strrev($url),'.')),'.');
list($server, $usrid)=explode('-', $url);
$server=(int)$server;
$usrid=(int)$usrid;

if(!mysql_select_db(dbname($server))) exit;
$usr=getuser($usrid);
if($usr===false || $usr['bigacc']!='yes') exit;
if($usr['enable_usrimg']=='no') exit;

header('Content-type: image/png', true);

$imgfile='data/_server'.$server.'/usrimgs/'.$usrid.'.png';
if(file_exists($imgfile)!=true) {
  $option=$usr['usrimg_fmt'];
  $ecnt=count(explode(' ',$option));
  
  #$hFont=imageloadfont('data/04b25.gdf');
  #$usr[name]=str_pad($usr[name].' ',40,'x');
  $w[0]=imagefontwidth(3)*strlen($usr['name']);
  if(stristr($option,'points')!=false) $w[1]=imagefontwidth(2)*strlen($usr['points'].' Punkte');
  if(stristr($option,'ranking')!=false) $w[2]=imagefontwidth(2)*strlen('Platz '.$usr['rank']);
  if(stristr($option,'cluster')!=false) {
    $c=getcluster($usr['cluster']);
    if($c!==false)
      $w[3]=imagefontwidth(2)*strlen($c['code']);
  }
  rsort($w);
  $w=$w[0]+25;
  $h=$ecnt*13+25;
  $hImg=imagecreate($w,$h);
  #$white=imagecolorallocate($hImg, 255, 255, 255);
  #imagecolortransparent($hImg, $white);
  $grey=imagecolorallocate($hImg, 240, 240, 240);
  $black=imagecolorallocate($hImg, 0, 0, 0);
  imagerectangle($hImg, 0, 0, $w-1, $h-1, $black);
  $darkred=imagecolorallocate($hImg, 128, 0, 0);
  imagestringup($hImg, 5, 1, $h-6, 'HTN', $darkred);
  imagestring($hImg, 3, 20, 2, $usr['name'], $black);
  imagestring($hImg, 1, 20, 15, 'Server '.$server, $black);
  $currenty=24;
  if(stristr($option,'cluster')!=false) { imagestring($hImg, 2, 20, $currenty, $c['code'], $black); $currenty+=12; }
  if(stristr($option,'points')!=false) { imagestring($hImg, 2, 20, $currenty, $usr['points'].' Punkte', $black); $currenty+=12; }
  if(stristr($option,'ranking')!=false) { imagestring($hImg, 2, 20, $currenty, 'Platz '.$usr['rank'], $black); $currenty+=12; }
  
  imagepng($hImg, $imgfile);
  chmod($imgfile, 0777);
  imagepng($hImg);
  imagedestroy($hImg);
} else {
  readfile($imgfile);
}

?>
