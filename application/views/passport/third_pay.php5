<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body onload="javascript:document.pay_form.submit();">
    <form id="pay_form" name="pay_form" action="<?php echo $action; ?>" method="<?php echo $method; ?>">
        <?php foreach ($params as $key => $value): ?>
        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
        <?php endforeach; ?>
        <input type="submit" type="hidden" style="display: none;">
    </form>
</body>
</html>
