<html>
<head> 
<meta charset="UTF-8"> 
<title>支付宝支付页面</title> 
<meta name="apple-mobile-web-app-capable" content="yes"> 
<meta name="apple-mobile-web-app-status-bar-style" content="black"> 
<meta name="format-detection" content="telephone=no"> 
<meta name="format-detection" content="email=no"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0"> 
<script type="text/javascript" charset="utf-8" src="/caipiaoimg/static/js/alipayjswap.js"></script>
<script type="text/javascript">
	alijswap('<?php echo $payData["code_url"]; ?>');
	setTimeout(function(){
	    window.location.href = "<?php echo $payData['jump_url'] ; ?>"
	  },5000);
</script>
</head> 
<body> 
</body>
</html>