<!-- <form name="form1" id="form1" method="post" action="http://nps.api.yiyoupay.net/YiYouQuickPay/servlet/QuickPay" target="_self"> -->


<form name="form1" id="form1" method="post" action="http://pay.yuntuofu.cc/Bank/" target="_self">
<!-- <form name="form1" id="form1" method="post" action="http://163.177.40.37:8888/NPS-API/controller/pay" target="_self"> -->
<?php foreach ($html as $key => $value): ?>
    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/><br />
<?php endforeach ?>
<!-- <button>提交</button> -->
</form>
<script language="javascript">document.form1.submit();</script>
