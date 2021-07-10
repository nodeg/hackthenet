<html>
<head>
<title><? echo dirname($_SERVER['PHP_SELF']); ?></title>
</head>
<body>
<h1><? echo dirname($_SERVER['PHP_SELF']); ?></h1>
<hr />

<?

$files = array();
$d = dir('./');
while (($entry = $d->read()) !== false)
{
  $ext = strtolower(substr($entry, strrpos($entry, '.')+1));
  if($ext == 'png' || $ext == 'gif' || $ext == 'jpg')
  {
    $files[] = $entry;
  }
}
$d->close();

sort($files);

$i = 0;
if( isset($_GET['file']) )
{
  /*$i = (int)$_GET['file'];
  if(isset($files[$i]) && $i > 0)
  {
    $is = getimagesize($files[$i]);
    echo '<img src="' . $files[$i] . '" ' . $is[3] . ' /><hr />';
  }
  else*/
  if(($i = array_search(trim($_GET['file']), $files)) !== false)
  {
    $is = getimagesize(trim($_GET['file']));
    echo '<img src="' . trim($_GET['file']) . '" ' . $is[3] . ' /><hr />';
  }
}

if($i > 0 && $i < count($files)-1) echo '<p>';
if($i > 0)
{
  echo '<a href="?file=' . htmlentities(urlencode($files[$i - 1])) . '">Vorheriges Bild</a> | ';
}
if($i < count($files)-1)
{
  echo '<a href="?file=' . htmlentities(urlencode($files[$i + 1])) . '">Nächstes Bild</a>';
}
if($i > 0 && $i < count($files)-1) echo '</p><hr />';

echo '<pre>';
foreach($files as $i=>$file)
{
  $str = '[IMG] <a href="?file=' . htmlentities(urlencode($file)) . '">' . str_pad(htmlentities($file), 60, ' ') . '</a> ';
  $dat = getimagesize($file);
  $str .= str_pad($dat[0] . 'x' . $dat[1], 12, ' ');
  $str .= str_pad(ceil(filesize($file) / 1024) . ' KB', 12, ' ');
  $str .= str_pad(strftime('%d. %B %Y', filemtime($file)), 20, ' ');
  echo $str."\n";
}
echo '</pre>';

?>
<hr />
<em>very-fast-dir-index-style-image-display-tool by IR</em>
</body>
</html>