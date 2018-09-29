<?= $info ?>
<form name="form1" id="form1" method="post" action="<?= $info['url'] ?>" target="_self">
<input type="hidden" name="pGateWayReq" value="<?= $info['content'] ?>" />
</form>
<script language="javascript">document.form1.submit();</script>