<html>
<head>
	<title>To Sumpay Page</title>
</head>
<body onLoad="document.sumpay.submit();">
	<form name='sumpay' action='<?php echo $payData['payUrl']; ?>' method='post'>
		<input type='hidden' name='sign_type' value='<?php echo $payData['sign_type']; ?>'>
		<input type='hidden' name='sign' value='<?php echo $payData['sign']; ?>'>
		<input type='hidden' name='app_id' value='<?php echo $payData['app_id']; ?>'>
		<input type='hidden' name='mer_id' value='<?php echo $payData['mer_id']; ?>'>
		<input type='hidden' name='cstno' value='<?php echo $payData['cstno']; ?>'>
		<input type='hidden' name='order_no' value='<?php echo $payData['order_no']; ?>'>
		<input type='hidden' name='cur_type' value='<?php echo $payData['cur_type']; ?>'>
		<input type='hidden' name='order_time' value='<?php echo $payData['order_time']; ?>'>
		<input type='hidden' name='order_amt' value='<?php echo $payData['order_amt']; ?>'>
		<input type='hidden' name='goods_name' value='<?php echo $payData['goods_name']; ?>'>
		<input type='hidden' name='goods_num' value='<?php echo $payData['goods_num']; ?>'>
		<input type='hidden' name='goods_type' value='<?php echo $payData['goods_type']; ?>'>
		<input type='hidden' name='logistics' value='<?php echo $payData['logistics']; ?>'>
		<input type='hidden' name='return_url' value='<?php echo $payData['return_url']; ?>'>
		<input type='hidden' name='notify_url' value='<?php echo $payData['notify_url']; ?>'>
		<input type='hidden' name='terminal_type'	value='<?php echo $payData['terminal_type']; ?>'>
		<input type='hidden' name='version' value='<?php echo $payData['version']; ?>'>
		<input type='hidden' name='service' value='<?php echo $payData['service']; ?>'>
		<input type='hidden' name='card_holder_name' value='<?php echo $payData['card_holder_name']; ?>'>
		<input type='hidden' name='cre_no' value='<?php echo $payData['cre_no']; ?>'>
		<input type='hidden' name='trade_code' value='<?php echo $payData['trade_code']; ?>'>
	</form>
</body>
<script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
<script>
	function postPayForm(){
		var tradeNo = sessionStorage.getItem("tradeNo");
		var payNo = '<?php echo $payData['order_no']; ?>';
		if(tradeNo != '' && tradeNo == payNo){
			window.history.back(); 
		}else{
			// 保存
			sessionStorage.setItem("tradeNo", payNo);
			document.sumpay.submit();
		}
	}
</script>
</html>