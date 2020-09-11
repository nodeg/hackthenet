<script type="text/javascript">
//<![CDATA[

// copied and adapted from phpBB
function insertText(form, sampleText) {
  sampleText = ' ' + sampleText + ' ';
	var txtarea = form.post_text;
	// IE
	if(document.selection && document.selection.createRange) { //navigator.appName == 'Microsoft Internet Explorer') {
		var theSelection = document.selection.createRange().text;
		if(!theSelection) { theSelection=sampleText;}
		txtarea.focus();
		if(theSelection.charAt(theSelection.length - 1) == " "){// exclude ending space char, if any
			theSelection = theSelection.substring(0, theSelection.length - 1);
			document.selection.createRange().text = sampleText + " ";
		} else {
			document.selection.createRange().text = theSelection;
		}

	// Mozilla
	} else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
 		var startPos = txtarea.selectionStart;
		var endPos = txtarea.selectionEnd;
		var scrollTop=txtarea.scrollTop;
		var myText = (txtarea.value).substring(startPos, endPos);
		if(!myText) { myText=sampleText;}
		if(myText.charAt(myText.length - 1) == " "){ // exclude ending space char, if any
			subst = myText.substring(0, (myText.length - 1)) + " ";
		} else {
			subst = myText;
		}
		txtarea.value = txtarea.value.substring(0, startPos) + subst +
		  txtarea.value.substring(endPos, txtarea.value.length);
		txtarea.focus();

		var cPos=startPos+myText.length;
		txtarea.selectionStart=cPos;
		txtarea.selectionEnd=cPos;
		txtarea.scrollTop=scrollTop;

	// All others
	} else {
		txtarea.value+=sampleText;
	}
	// reposition cursor if possible
	if (txtarea.createTextRange) txtarea.caretPos = document.selection.createRange().duplicate();
	return true;
}

function bbcode_insert(butt)
{
  var tag = butt.title;
  var tmp = tag.match(/^\[(\/)?([\w]*)\]$/);
  insertText(butt.form, butt.title);
  if(tmp[1] != '/')
  {
    butt.title = '[/' + tmp[2] + ']';
    butt.value = '/' + tmp[2];
  }
  else
  {
    butt.title = '[' + tmp[2] + ']';
    butt.value = tmp[2];
  }
}

//]]>
</script>


<?

if($error_occured)
{
  echo htnhtml::error_box('Fehler', $error_msg);
}

if(!isset($close_permitted))
{
  $close_permitted = is_permitted_u($usr, $forum->id, A_CLOSE_TOPIC);
  $close_topic = false;
}

?>


<form action="<?=$form_action?>" method="post" onsubmit="this.submit();this.submit.disabled=true; this.submit.value='Bitte warten...';">

<table>

<tr><th<?=($title_required ? ' rowspan="2"' : '')?>>Titel:<?=($title_required ? '' : '<span class="explanation"><br />optional</span>')?></th>
<td><input class="stylish" tabindex="1" type="text" name="post_subject" size="50" maxlength="64" value="<?=htmlspecialchars($subject)?>" /></td></tr>
<?=($title_required ? '<tr><td class="explanation">Wähle einen möglichst aussagekräftigen Titel, also z.B. "Was ist X?" und nicht "Frage".</td></tr>' : '')?>
<tr><th rowspan="3">Text:</th>
<td class="bbcodebuttons">
<script type="text/javascript">
//<![CDATA[
var bbcodes = new Array('b', 'u', 'i', 'quote', 'box', 'code', 's');
for(var i = 0; i < bbcodes.length; i++)
{
  document.write('<input type="button" class="flatbutton" value="' + bbcodes[i] + '" title="[' + bbcodes[i] + ']" onclick="bbcode_insert(this)" /> ');
}
//]]>
</script>
</td></tr>
<tr><td><textarea tabindex="2" class="stylish" name="post_text" rows="10" cols="80"><?=htmlspecialchars($text)?></textarea></td></tr>
<tr><td>
<?
include 'core/smilie-data.php';
foreach($smilies as $symbol => $file)
{
  echo '<a href="javascript:void(0);" onclick="return insertText(document.forms[0], \''.htmlspecialchars($symbol).'\')"><img src="' . FORUM_BASEDIR . '/media/smilies/' . $file . '" /></a> ';
}
?>
</td></tr>
<tr><th rowspan="2">Optionen:</th><td>
<?=htnhtml::form_checkbox('parse_bbcode', $parse_bbcode, 'BB-Code und URLs in diesem Beitrag umwandeln')?><br />
<?=htnhtml::form_checkbox('replace_smilies', $replace_smilies, 'Text-Smilies durch Grafiken ersetzen')?>
</td></tr>
<tr><td>
<?
if(($is_new_topic || $edit) && $sticky_topic_permitted)
{
  echo htnhtml::form_checkbox('sticky_topic', $sticky_topic, 'Thema sticky machen') . '<br />';
}
if($close_permitted)
{
  echo htnhtml::form_checkbox('close_topic', $close_topic, 'Thema schließen')  . '<br />';
}
echo htnhtml::form_checkbox('watch_topic', $watch_topic, 'Bei neuen Antworten per Email benachrichtigen')  . '<br />';
?>
</td></tr>
<tr class="submit"><td colspan="2">
<input type="submit" value="Vorschau" class="flatbutton" name="preview" />
&nbsp;
<input type="submit" value="    Absenden    " class="flatbutton" accesskey="s" name="submit" /></td>
</tr>

</table>

</form>