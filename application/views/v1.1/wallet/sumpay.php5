<html>
<head>
<title>To SumPay Page</title>
</head>
<body onLoad="document.sumpay.submit();">
<form name='sumpay' action='<?php echo $params['payUrl']; ?>' method='post'>
<input type='hidden' name='sign_type'	value='<?php echo $params['sign_type']; ?>'>
<input type='hidden' name='sign'		value='<?php echo $params['sign']; ?>'>
<input type='hidden' name='mer_id'		value='<?php echo $params['mer_id']; ?>'>
<input type='hidden' name='app_id'		value='<?php echo $params['app_id']; ?>'>
<input type='hidden' name='cstno'		value='<?php echo $params['cstno']; ?>'>
<input type='hidden' name='order_no'	value='<?php echo $params['order_no']; ?>'>
<input type='hidden' name='cur_type'	value='<?php echo $params['cur_type']; ?>'>
<input type='hidden' name='order_time'	value='<?php echo $params['order_time']; ?>'>
<input type='hidden' name='order_amt'	value='<?php echo $params['order_amt']; ?>'>
<input type='hidden' name='notify_url'	value='<?php echo $params['notify_url']; ?>'>
<input type='hidden' name='return_url'	value='<?php echo $params['return_url']; ?>'>
<input type='hidden' name='goods_name'	value='<?php echo $params['goods_name']; ?>'>
<input type='hidden' name='goods_num'	value='<?php echo $params['goods_num']; ?>'>
<input type='hidden' name='goods_type'	value='<?php echo $params['goods_type']; ?>'>
<input type='hidden' name='logistics'	value='<?php echo $params['logistics']; ?>'>
<input type='hidden' name='terminal_type'	value='<?php echo $params['terminal_type']; ?>'>
<input type='hidden' name='version'	value='<?php echo $params['version']; ?>'>
<input type='hidden' name='service'	value='<?php echo $params['service']; ?>'>
<input type='hidden' name='timestamp'	value='<?php echo $params['timestamp']; ?>'>
<input type='hidden' name='trade_code'	value='<?php echo $params['trade_code']; ?>'>
<input type='hidden' name='cre_no'		value='<?php echo $params['cre_no']; ?>'>
<input type='hidden' name='card_holder_name'	value='<?php echo $params['card_holder_name']; ?>'>
</form>
</body>
</html>