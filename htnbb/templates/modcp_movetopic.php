<div class="content" id="modcp">
<h2>Moderation</h2>

<h3 class="transfer">Thema verschieben</h3>

<form action="<?=bb_url('modcp', 'movetopic', 'topic='.$topic_id)?>" method="post">
<table>
<tr><th>Ziel-Forum:</th><td><?=htnhtml::code_form_selectbox('forum', $forums, $forum_id, 0)?></td></tr>
<?=htnhtml::form_submit_row('Verschieben')?>
</table>
</form>

</div>