<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <meta http-equiv="Pragma" content="no-cache">
    <title>充值</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/recharge.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/pay.min.css');?>">
</head>
<body>
    <div class="wrapper pay ios">
        <div class="m-header">
            <header>
                <h1>支付</h1>
                <a href="<?php echo BackToLottery('0');?>" class="hd-lnk-l" id="goBack">返回客户端</a>
            </header>
        </div>    
        <form id="doPayForm" action="/ios/wallet/doPayForm" method="post" class="no-top cp-form"> 
            <div class="cp-form-group box-wave mb30">
                <div class="cp-form-item">
                    <label for="">充值金额:</label>
                    <span><?php echo $rechargeData['money']; ?>元</span>
                </div>
                <div class="cp-form-item">
                    <label for="">使用红包:</label>
                    <span><?php echo $redpackInfo?$redpackInfo:'未使用'; ?></span>
                </div>
                <div class="cp-form-item">
                    <label for="">实际到账:</label>
                    <span><?php echo $rechargeData['addMoney']; ?>元</span>
                </div>
            </div>


            <ul class="cp-list cp-list-senior recharge-way-group">
                <!-- <li>
                    <div class="cp-list-txt">
                        <input type="radio" id="payYlWap" name="rechargeWay" pay-data="shengpaywap">
                        <label for="payYlWap" class="pay-ylwap">
                            <b>银行卡WAP</b>
                            <small>无需开通网银，快速支付</small>
                        </label>
                    </div>
                </li> -->
                <li>
                    <div class="cp-list-txt">
                        <input type="radio" id="payYbzf" name="rechargeWay" pay-data="yeepayMPay" checked>
                        <label for="payYbzf" class="pay-ybzf">
                            <b>银行卡快捷-易宝支付</b>
                            <small>最高5千/笔，1万/日，2万/月</small>
                        </label>
                    </div>
                </li>
                <li>
                    <div class="cp-list-txt">
                        <input type="radio" id="payTtf" name="rechargeWay" pay-data="sumpayWap">
                        <label for="payTtf" class="pay-ttf">
                            <b>银行卡快捷-统统付</b>
                            <small>最高3千/笔，5千/日，1万/月</small>
                        </label>
                    </div>
                </li>
            </ul>
            
            <div class="btn-group">
                <a class="btn btn-block-confirm btn-recharge" id="recharge-confirm" href="javascript:void(0)">立即支付</a>
            </div>
            <input type='hidden' class='' name='payType' value=''/>
            <input type='hidden' class='' name='rechargeParmas' value=''/>
        </form>
        <aside class="recharge-tips">
            <ol>
                <li>1.为防止恶意提现、洗钱等不法行为，信用卡充值不可提现，储蓄卡每笔充值至少50%需用于购彩；</li>
                <li>2.使用充值红包后的单笔充值本金与红包均不可提现；</li>
                <li>3.奖金可以提现，无限制；</li>
                <li>4.大额充值请登录网页<?php echo $this->config->item('domain'); ?>使用网上银行充值。</li>
            </ol>
        </aside>
        <input type='hidden' class='' name='token' value='<?php echo $rechargeToken;?>'/>
        <input type='hidden' class='' name='sign' value='<?php echo $sign;?>'/>
    </div>
    
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        // 基础配置
        require.config({
            baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
            paths: {
                "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
                "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
                'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
            }
        })
        require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){

            // 充值
            var closeTag = true;
            var lastDate = 0;

            $('#recharge-confirm').on('tap', function(){

                var payType = $('input[name="rechargeWay"]:checked').attr('pay-data');
                var token = $('input[name="token"]').val();
                var sign = $('input[name="sign"]').val();

                if(payType == '' || typeof(payType) == 'undefined')
                {
                    $.tips({
                        content: '请选择充值方式',
                        stayTime: 2000
                    })
                    return false;
                }

                $('#doPayForm input[name="payType"]').val(payType);
         
                // 提交充值记录
                if(closeTag)
                {
                    // 获取当前时间戳
                    var tagDate = new Date();
                    var time = tagDate.getTime() - lastDate;
                    if(0 < time && time < 450)
                    {
                        return false;
                    }
                    lastDate = tagDate.getTime();

                    closeTag = false;

                    // 浮层
                    var loadPay = $.loading().loading("mask");

                    $.ajax({
                        type: 'post',
                        url: '/ios/wallet/jumpRecharge',
                        data: {payType:payType,token:token,sign:sign},
                        // beforeSend: loading,
                        success: function (response) {
                            var response = $.parseJSON(response);
                            loadPay.loading("hide");
                            if(response.status == '1')
                            {
                                $('#doPayForm input[name="rechargeParmas"]').val(response.data);
                                $('#doPayForm').submit();
                                closeTag = true;
                            }else{
                                closeTag = true;
                                $.tips({
                                    content: response.msg,
                                    stayTime: 2000
                                })
                            }
                        },
                        error: function () {
                            closeTag = true;
                            loadPay.loading("hide");
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                }
                
            })
            
            

        });
    </script>
<?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>