<html>
<head>
<title>To YeePay Page</title>
</head>
<body onLoad="document.yeepay.submit(); ">
<form name='yeepay' action='<?php echo $params['reqURL_onLine']; ?>' method='post' accept-charset='gbk'>
<input type='hidden' name='p0_Cmd'		value='<?php echo $params['p0_Cmd']; ?>'>
<input type='hidden' name='p1_MerId'	value='<?php echo $params['p1_MerId']; ?>'>
<input type='hidden' name='p2_Order'	value='<?php echo $params['p2_Order']; ?>'>
<input type='hidden' name='p3_Amt'		value='<?php echo $params['p3_Amt']; ?>'>
<input type='hidden' name='p4_Cur'		value='<?php echo $params['p4_Cur']; ?>'>
<input type='hidden' name='p5_Pid'		value='<?php echo $params['p5_Pid']; ?>'>
<input type='hidden' name='p6_Pcat'		value='<?php echo $params['p6_Pcat']; ?>'>
<input type='hidden' name='p7_Pdesc'	value='<?php echo $params['p7_Pdesc']; ?>'>
<input type='hidden' name='p8_Url'		value='<?php echo $params['p8_Url']; ?>'>
<input type='hidden' name='p9_SAF'		value='<?php echo $params['p9_SAF']; ?>'>
<input type='hidden' name='pa_MP'		value='<?php echo $params['pa_MP']; ?>'>
<input type='hidden' name='pd_FrpId'	value='<?php echo $params['pd_FrpId']; ?>'>
<input type='hidden' name='pm_Period'	value='<?php echo $params['pm_Period']; ?>'>
<input type='hidden' name='pn_Unit'		value='<?php echo $params['pn_Unit']; ?>'>
<input type='hidden' name='pr_NeedResponse'	value='<?php echo $params['pr_NeedResponse']; ?>'>
<input type='hidden' name='hmac'		value='<?php echo $params['hmac']; ?>'>
</form>
</body>
</html>
