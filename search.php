<?php

// nie fertig geworden!

define('IN_HTN',1);
$FILE_REQUIRES_PC=FALSE;
include('ingame.php');

$action=$_REQUEST['page'];
if($action=='') $action=$_REQUEST['mode'];
if($action=='') $action=$_REQUEST['action'];
if($action=='') $action=$_REQUEST['a'];
if($action=='') $action=$_REQUEST['m'];

function showform() {
global $sid;
echo '<div id="search-form">
<h3>HackTheNet-Suche</h3>
<!--
<h4>Listen</h4>
<p>
<a href="search.php?m=list&amp;sid='.$sid.'&amp;type=user">Spieler</a> |
<a href="search.php?m=list&amp;sid='.$sid.'&amp;type=pcs">Computer</a> |
<a href="search.php?m=list&amp;sid='.$sid.'&amp;type=cluster">Cluster</a>
</p>
//-->
<form action="search.php?sid='.$sid.'&amp;a=exec" method="post">
<h4>Suche nach:</h4>
<p>
<input type="radio" name="for" value="user" id="search_for_user" checked="checked" onclick="showpan(this)" />
<label for="search_for_user">Spielern</label>
<input type="radio" name="for" value="pcs" id="search_for_pcs" onclick="showpan(this)" />
<label for="search_for_pcs">Computern</label>
<input type="radio" name="for" value="cluster" id="search_for_cluster" onclick="showpan(this)" />
<label for="search_for_cluster">Clustern</label>
</p>

<div id="search-form-user" style="display:block;">
<h4>Suche nach Spielern</h4>
<p>Das H&auml;cken entfernen um beliebige Werte zuzulassen.</p>

<table>

<tr>
<th>Name:</th>
<td><input type="checkbox" name="user_use_name" value="yes" checked="checked" title="Wert beachten" /></td>
<td>
  <select name="user_name_opt">
  <option value="exact">stimmt genau &uuml;berein</option>
  <option value="contains">enth&auml;lt</option>
  </select>
</td>
<td>
  <input type="text" name="user_name_string" />
</td>
</tr>

<tr>
<th>Punkte:</th>
<td><input type="checkbox" name="user_use_points" value="yes" checked="checked" title="Wert beachten" /></td>
<td>
  <select name="user_points_opt">
  <option value="min">mindestens</option>
  <option value="equal">genau</option>
  <option value="max">h&ouml;chstens</option>
  </select>
</td>
<td>
  <input type="text" name="user_points_string" />
</td>
</tr>

<tr>
<th>Cluster-Name:</th>
<td><input type="checkbox" name="user_use_cluster_name" value="yes" checked="checked" title="Wert beachten" /></td>
<td>
  <select name="user_cluster_name_opt">
  <option value="exact">stimmt genau &uuml;berein</option>
  <option value="contains">enth&auml;lt</option>
  </select>
</td>
<td>
  <input type="text" name="user_cluster_name" />
</td>
</tr>

<tr>
<th>Cluster-Code:</th>
<td><input type="checkbox" name="user_use_cluster_code" value="yes" checked="checked" title="Wert beachten" /></td>
<td>&nbsp;</td>
<td>
  <input type="text" name="user_cluster_code" />
</td>
</tr>

</table>

</div>

<div id="search-form-pcs" style="display:none;">
<h4>Suche nach Computern</h4>
<p>Das H&auml;cken entfernen um beliebige Werte zuzulassen.</p>

<table>
<tr>
<th>IP:</th>
<td><input type="checkbox" name="pcs_use_ip" value="yes" checked="checked" title="Wert beachten" /></td>
<td>
  * als Wildcard
</td>
<td>
  <input type="text" name="pcs_ip_string" value="10.47." />
</td>
</tr>

<tr>
<th>Besitzer:</th>
<td><input type="checkbox" name="pcs_use_owner" value="yes" checked="checked" title="Wert beachten" /></td>
<td>
  <select name="pcs_owner_opt">
  <option value="exact">stimmt genau &uuml;berein</option>
  <option value="contains">enth&auml;lt</option>
  <option value="noowneronly">nur PCs ohne Besitzer</option>
  </select>
</td>
<td>
  <input type="text" name="pcs_owner_string" />
</td>
</tr>

<tr>
<th>Punkte:</th>
<td><input type="checkbox" name="pcs_use_points" value="yes" checked="checked" title="Wert beachten" /></td>
<td>
  <select name="pcs_points_opt">
  <option value="min">mindestens</option>
  <option value="equal">genau</option>
  <option value="max">h&ouml;chstens</option>
  </select>
</td>
<td>
  <input type="text" name="pcs_points_string" />
</td>
</tr>
</table>

</div>

<div id="search-form-cluster" style="display:none;">
<h4>Suche nach Clustern</h4>
<p>Das H&auml;cken entfernen um beliebige Werte zuzulassen.</p>

</div>

</form>
</div>';
}

switch($action) {

case 'form': // ----------------------------------- FORM --------------------------------
$javascript='<script type="text/javascript">
function showpan(obj) {
var pan=getLay(\'search-form-\'+obj.value);
getLay(\'search-form-user\').style.display=\'none\';
getLay(\'search-form-pcs\').style.display=\'none\';
getLay(\'search-form-cluster\').style.display=\'none\';
pan.style.display=\'block\';
}
</script>
';
createlayout_top('HackTheNet - Suche');
echo '<div class="content" id="search">'."\n";
echo '<h2>Suche</h2>'."\n";
showform();
echo '</div>'."\n";
createlayout_bottom();
break;


case 'exec':  // ----------------------------------- EXEC --------------------------------

break;

}

?>
