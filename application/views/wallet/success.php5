<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>支付结果</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/recharge.min.css');?>">
</head>
<body>
    <div class="wrapper recharge-result">
        <div class="recharge-result-bd">
            <div class="recharge-result-loading">
                <i></i>
                支付结果获取中...
            </div>
        </div>
        <div class="recharge-result-false" style="display: none;">
            <div class="it-img">
                <img src="data:image/png;base64,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" alt="">
            </div>
            <div class="it-txt">
                <h1>返回166彩票客户端</h1>
                <p>查看充值结果</p>
            </div>
            
        </div>
        <div class="pay-result" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: #fff; display: none;">
            <div class="recharge-detail-hd mod-result">
                <div class="mod-result-hd">
                    <h1 class="recharge-detail-title mod-result-title">充值成功！</h1>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        var count = 0;
        $(function(){
            setTimeout('checkPay()', 2000);            
        });
        function checkPay(){
            // 查询
            $.ajax({
                type: 'post',
                url: '<?php echo $url;?>',
                success: function (response) 
                {
                    var response = $.parseJSON(response);
                    if(response.code==1)
                    {
                        alert(response.msg);
                    }else{
                        if(response.ispay && response.pstatus=='已付款'){
                            $('.pay-result').show();
                        }
                        else if(!response.ispay && count >20)
                        {
                            $('.recharge-result-false').show();
                        }else{
                            count++;
                            setTimeout('checkPay()', 3000);
                        }
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