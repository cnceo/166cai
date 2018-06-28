<html>
<head>
	<title>To Alipay Page</title>
</head>
<body onLoad="document.alipay.submit();">
	<form name='alipay' action='<?php echo $payData['action']; ?>' method='post'>
		<input type="hidden" name="biz_content" value="<?php echo $payData['biz_content'];?>">
	</form>
</body>
<script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
<script>
	// function postPayForm(){
	// 	var tradeNo = sessionStorage.getItem("tradeNo");
	// 	var payNo = '<?php echo $payData['order_no']; ?>';
	// 	if(tradeNo != '' && tradeNo == payNo){
	// 		window.history.back(); 
	// 	}else{
	// 		// 保存
	// 		sessionStorage.setItem("tradeNo", payNo);
	// 		document.sumpay.submit();
	// 	}
	// }
</script>
</html>