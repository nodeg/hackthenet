<?php
error_reporting(E_ALL);
include_once('config.php');

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($database_prefix.$database_suffix);
mysql_query('TRUNCATE TABLE verify_imgs;');

$verz='verifyimgs';

$h=@opendir($verz);
  while($fn=@readdir($h)) {
    if(@is_file($verz.'/'.$fn)) {
      @unlink($verz.'/'.$fn);
    }
  }
@closedir($h);

function RandomX($chars=6,$cs=true) { //------------------------ RandomX --------------------------
mt_srand((double)microtime()*1000000);
$s='';
$aa=ord('a');

$AA=ord('A');
for($i=0;$i<$chars*2;$i++) {
  if(mt_rand(0,1)==1) {
    $s.=mt_rand(1,9);
  } else {
    mt_srand((double)microtime()*1000000);
    if(mt_rand(0,1)==0 OR $cs==false) {
      $s.=chr(mt_rand(0,25)+$aa);
    } else {
      $s.=chr(mt_rand(0,25)+$AA);
    }
  }
}
$s=substr($s,mt_rand(0,$chars),$chars);
return $s;
}

#header('content-type: image/png');

@$fonts[1]=imageloadfont('data/atommicclock.gdf');
@$fonts[0]=imageloadfont('data/almosnow.gdf');
#$fonts[1]=imageloadfont('data/borringlesson.gdf');
#$fonts[2]=imageloadfont('data/caveman.gdf');
#$fonts[2]=imageloadfont('data/crass.gdf');
#$fonts[1]=imageloadfont('data/ennobled.gdf');

$chars='';

function print_char($x)
{
  global $hImg, $fonts, $chars, $clr_ix;
  
  switch(mt_rand(0,2))
  {
  case 0:
    $char=chr(mt_rand(65, 90));
    $hFont=mt_rand(0,count($fonts)-1);
    break;
  case 1:
    $char=mt_rand(0, 9);
    $hFont=0;
    break;
  case 2:
    $char=chr(mt_rand(97, 122));
    $hFont=mt_rand(0,count($fonts)-1);
    break;
  }
  
  $chars.=$char;
  
  if($clr_ix == 0)
  {
    $forecolor=imagecolorallocate($hImg, mt_rand(0,255), mt_rand(0,100), 0);
  }
  else
  {
    $forecolor=imagecolorallocate($hImg, mt_rand(0,255), 255, mt_rand(0,255));
  }
  
  $hFont=$fonts[$hFont];
  $y=mt_rand(1, 50-imagefontheight($hFont));
  
  imagestring($hImg, $hFont, $x, $y, $char, $forecolor);
}
  

for($i=0; $i<1000; $i++)
{
  
  $hImg=imagecreate(100, 50);
  
  $clr_ix=mt_rand(0,1);
  if($clr_ix == 1)
  {
    $bgcolor=imagecolorallocate($hImg, mt_rand(0,255), mt_rand(0,100), 0);
  }
  else
  {
    $bgcolor=imagecolorallocate($hImg, mt_rand(0,255), 255, mt_rand(0,255));
  }
  
  ImageFill($hImg, 0, 0, $bgcolor);
  
  //int imagestring ( int im, int font, int x, int y, string s, int col)

imageline ($hImg, mt_rand(-100, 1), mt_rand(-50,100), mt_rand(100,200), mt_rand(-50, 100), 1);
imageline ($hImg, mt_rand(-100, 1), mt_rand(-50, 100), mt_rand(100,200), mt_rand(-50, 100), 2);
imageline ($hImg, mt_rand(-100, 1), mt_rand(-50, 100), mt_rand(100,200), mt_rand(-50, 100), 3);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 10, 10, 2);
#imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 20, 20, 2);
#imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 30, 30, 2);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 2);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 3);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 1);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 2);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 3);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 1);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 2);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 3);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 1);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 2);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 3);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 1);

imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 2);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 3);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 1);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 2);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 3);
imageellipse($hImg, mt_rand(1, 100), mt_rand(1, 50), 1, 1, 1);

  
  $chars='';
  print_char(mt_rand(1, 10));
  print_char(mt_rand(35, 40));
  print_char(mt_rand(60, 70));
  
  $key=randomx(6);
  
  mysql_query('INSERT INTO verify_imgs VALUES(0, \''.$key.'\', \''.$chars.'\');');
  
  imagepng($hImg, 'verifyimgs/'.$key.'.png');
  imagedestroy($hImg);
}
echo '<p>Anti-Bot Bilder wurden erstellt</p>';

?>
