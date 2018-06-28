<html>
<head> 
<meta charset="UTF-8"> 
<title>支付宝支付页面</title> 
<meta name="apple-mobile-web-app-capable" content="yes"> 
<meta name="apple-mobile-web-app-status-bar-style" content="black"> 
<meta name="format-detection" content="telephone=no"> 
<meta name="format-detection" content="email=no"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0"> 
</head> 
<body> 
<script type="text/javascript" charset="utf-8" src="<?php echo getStaticFile('/caipiaoimg/static/js/alipaywap.js');?>" type="text/javascript"></script>
<script>
alipay_wap('<?php echo $payData['prepay_id'] ; ?>','<?php echo $payData['jump_url'] ; ?>');
</script>
</body>
</html>