<form name="form1" id="form1" method="post" action="<?= $html['urll'] ?>" target="_self">
        <input type="hidden" name="parter" value="<?= $html['parter'] ?>">
        <input type="hidden" name="bank" value="<?= $html['bank'] ?>">
        <input type="hidden" name="value" value="<?= $html['value'] ?>">
        <input type="hidden" name="orderid" value="<?= $html['orderid'] ?>">
        <input type="hidden" name="callbackurl" value="<?= $html['callbackurl'] ?>">
        <input type="hidden" name="hrefbackurl" value="<?= $html['hrefbackurl'] ?>">
        <input type="hidden" name="sign" value="<?= $html['sign'] ?>">

</form>
<script language="javascript">document.form1.submit();</script>


<?= $html['bank'] ?>