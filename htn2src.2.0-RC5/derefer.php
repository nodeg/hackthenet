<?php

$url=$_GET['u'];
$url=urldecode($url);

if(eregi('http://.*',$url)!=false) {
echo '<html>
<head>
<meta http-equiv="REFRESH" content="0; URL='.$url.'">
</head>
<body>
<strong>Du wirst weitergeleitet!</strong><br />
Wenn das nicht klappt, klick hier: <a href="'.$url.'">$url</a>
</body>
</html>
';
}

?>
