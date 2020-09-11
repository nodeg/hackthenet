<html><head>
<title>Land wählen</title>

<script language="JavaScript" type="text/javascript" src="../global.js"></script>
<script language="JavaScript" type="text/javascript" src="selcountry.js"></script>
<script language="JavaScript" type="text/javascript">
// @ by I.Runge 2004

function selectCountry(name) {
//opener.xform.feld.value="name";
opener.subnetgo(name);
self.close();
}

</script>

</head>
<body style="font-family:arial;">

<?php
define("IN_HTN",1);
include("../gres.php");
echo str_replace("%path%","../images/maps",file_get("../data/pubtxt/selcountry_body.txt"));
?>

</body>
</html>