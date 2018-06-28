<!doctype html> 
<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>微信支付</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/wx-pay.css');?>">
</head>
<body>
    <div class="wrapper ios">
        <div class="m-header">
            <header>
                <h1>微信支付</h1>
                <a href="<?php echo BackToLottery('0');?>" class="hd-lnk-l hd-back">返回</a>
            </header>
        </div>
        <div class="container">
            <div class="qr-code">
                <p>¥<?php echo number_format(ParseUnit($params['txnAmt'], 1), 2);?></p>
                <img src="/app/wallet/qrCode/<?php echo urlencode(base64_encode($params['codeUrl']));?>" alt="">
            </div>
            <div class="desc">
                <p>1、手机截图保存此页面</p>
                <p>2、打开微信『扫一扫』识别二维码付款<a href="/ios/wallet/wxintro" target="_blank">图文介绍</a></p>
            </div>
        </div>
        <footer class="footer">
            <p>充值成功后页面将自动跳转</p>
        </footer>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>    
        // 判断是否是IOS
//         if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
//             document.querySelector('.wrapper').className += " ios";
//         }
    </script>
    <script>
        $(function(){
            setTimeout('checkPay()', 5000);            
        });

        var token = "<?php echo $params['token']; ?>";
        var num = 100;
        var flag = 1;

        function checkPay(){
            // 查询
            $.ajax({
                type: 'post',
                url: '/ios/wallet/getRechargeStatus',
                data: {token:token, num:num, flag:flag},
                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 1){
                        window.location.href = response.data;
                    }else if(response.status == 2){
                        return false;
                    }else{
                        setTimeout('checkPay()', 3000);
                    }
                },
                error: function () {
                    return false;
                }
            });  
        }
    </script>
</body>
</html>