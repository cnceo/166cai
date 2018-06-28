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
    <title>开售有好礼</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/czscj.min.css')?>">
</head>
<body ontouchstart="">
    <div class="wrap">
        <div class="wrap-content">
            <?php if($hasAttend == '1'): ?>
            <div class="hb-after">
                <a href="javascript:void(0);" onclick="window.webkit.messageHandlers.goRedpack.postMessage({});" class="btn-click" id="btn-click">查看红包</a>
                <h2>已领取过红包</h2>
            </div>
            <?php else: ?>
            <div class="hb" id="hb">
                <a href="javascript:void(0);" class="btn-click" id="btn-click">点击领取</a>
                <h2>红包领取成功</h2>
            </div>
            <?php endif; ?>
            <div class="active-rule">
                <h2>活动规则</h2>
                <ul class="rule-list">
                    <li data-num='1'>活动时间：<?php echo date('Y.m.d', strtotime($startTime))?>-<?php echo date('Y.m.d', strtotime($endTime))?></li>
                    <li data-num='2'>活动限新用户参加，进入活动页面，验证手机号后即可领取红包，每位用户限领一次。</li>
                    <li data-num='3'>188元红包价值如下：<br>a. 3元注册红包（实名认证后可用）<br>b. 2元红包（充值20元及以上可用），5个<br>c. 5元红包（充值50元及以上可用），5个<br>d. 10元红包（充值100元及以上可用），5个<br>e. 20元红包（充值200元及以上可用），5个</li>
                    <li data-num='4'>红包有效期为30天，逾期未使用的红包将被系统收回。</li>
                    <li data-num='5'>充值时手动勾选充值红包即可使用。</li>
                    <li data-num='6'>活动充值与赠送的红包均不能提现，只能用于购彩，中奖奖金可提现。</li>
                    <li data-num='7'>活动过程中如用户通过不正当手段领取彩金，166彩票网有权不予赠送、限制提款、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。</li>
                    <li data-num='8'>关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
                </ul>
            </div>
        </div> 
    </div>
</body>
<script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>"></script>
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
        var closeTag = true;
        $('.hb').on('tap', '.btn-click', function(){
            var $this = $(this);
            var Bcp = $(this).parents('div');
            if(Bcp.hasClass('hb-after')){
                // 红包详情
                window.webkit.messageHandlers.goRedpack.postMessage({});
            }else{
                if(closeTag){
                closeTag = false;
                    var showLoading = $.loading().loading("mask");
                    $.ajax({
                        type: "post",
                        url: '/ios/activity/innerAttend',
                        data: {},                   
                        success: function (data) {
                            showLoading.loading("hide");
                            var data = $.parseJSON(data);
                            if(data.status == '200')
                            {
                                $this.html('查看红包').parents('.hb').addClass('hb-after');
                                $.tips({
                                    content:data.msg,
                                    stayTime:2000
                                });
                            }
                            else if(data.status == '100')
                            {
                                // 未登录
                                closeTag = true;
                                var backUrl = window.location.href;
                                window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                            }
                            else
                            {
                                closeTag = true;
                                $.tips({
                                    content:data.msg,
                                    stayTime:2000
                                });
                            }
                        },
                        error: function () {
                            closeTag = true;
                            showLoading.loading("hide");
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                }
            }       
        });    
    });
</script>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>