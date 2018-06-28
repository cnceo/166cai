<html>
<head>
<title>To ZfbPay Page</title>
</head>
<body onLoad="document.wftZfb.submit();">
<form name='wftZfb' action='/wallet/getwftZfb' method='post'>
<input type='hidden' name='orderId' value='<?php echo $params['orderId']; ?>'>
<input type='hidden' name='orderTime' value='<?php echo $params['orderTime']; ?>'>
<input type='hidden' name='txnAmt'	value='<?php echo $params['txnAmt']; ?>'>
<input type='hidden' name='codeUrl'	value='<?php echo $params['codeUrl']; ?>'>
</form>
</body>
</html>
