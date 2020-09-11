<?php

ignore_user_abort(0);
set_time_limit(1200);

define('IN_HTN',1);
include 'gres.php';

$secret_code = 'sdap432i';

if($_GET['code'] != $secret_code) exit;

$server = (int)$_GET['server'];
if($server == 0) $server = 1;
db_select(dbname($server));
db_query('TRUNCATE TABLE verifyimgs');

$fonts[0]=imageloadfont('data/atommicclock.gdf');
$fonts[1]=imageloadfont('data/backlash.gdf');

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
    $forecolor = imagecolorallocate($hImg, mt_rand(0,255), mt_rand(0,100), 0);
  }
  else
  {
    $forecolor = imagecolorallocate($hImg, mt_rand(0,255), 255, mt_rand(0,255));
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
  
  $chars='';
  print_char(mt_rand(1, 10));
  print_char(mt_rand(35, 40));
  print_char(mt_rand(60, 70));
  
  imagepng($hImg, 'data/_server' . $server . '/verifyimgs/' . ($i+1) . '.png');
  db_query('INSERT INTO verifyimgs SET id=' . ($i+1) . ', chars=\''. $chars . '\'');
  
  imagedestroy($hImg);
}
die('ok');

?>