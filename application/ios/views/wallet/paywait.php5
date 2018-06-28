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
    <title>支付结果</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/recharge.min.css');?>">
</head>
<body>
    <div class="wrapper recharge-result ios">
        <div class="m-header">
            <header>
                <h1>支付结果</h1>
            </header>
        </div>    
        <div class="recharge-result-bd">
            <div class="recharge-result-loading">
                <i></i>
                支付结果获取中...
            </div>
        </div>

        <div class="recharge-result-ft">
            <div class="btn-group">
                <a class="btn btn-block-confirm btn-recharge" href="<?php echo BackToLottery('3');?>">返回个人中心</a>
            </div>
            <aside class="recharge-tips">
                <ul>
                    <li>如页面长时间未自动跳转，且银行卡已扣款，请点击「返回个人中心」查看是否充值成功。</li>
                    <li>如有疑问，请及时与客服联系确认。</li>
                    <li>客服电话：<a href="tel:4006906760"></a>4006906760</li>
                </ul>
            </aside>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>    
        // 判断是否是IOS
        if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
            document.querySelector('.wrapper').className += " ios";
        }
    </script>
    <script>
        $(function(){
            setTimeout('checkPay()', 5000);            
        });

        var token = '<?php echo $token; ?>';

        function checkPay(){
            // 查询
            $.ajax({
                type: 'post',
                url: '/ios/wallet/getRechargeStatus',
                data: {token:token},
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
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>