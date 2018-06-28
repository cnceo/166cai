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
    <title>166彩票火爆开售</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/czscj.min.css')?>">
</head>
<body ontouchstart="">
    <div style="display: none;">
        <img src="<?php echo getStaticFile('/caipiaoimg/static/img/logo.png') ?>" width="300" height="300" alt="">
    </div>
    <div class="wrap">
        <div class="wrap-content">
            <!-- 领取前 -->
            <div class="hb-wai" style="display:block">
                <input type="tel" placeholder="输入手机号，领取红包" maxlength="11" class="phone-num" name="phone-num">
                <p class="p-false" id="phoneError">手机号错误</p>
                <div class="yzm">
                    <input type="tel" placeholder="输入4位验证码" maxlength="4" class="input-yzm" name="input-txyzm">
                    <img id="imgCaptcha" src="/app/activity/captcha" alt="">
                    <a href="javascript:" class="change-img" id="change_imgCaptcha">换一张</a>
                </div>
                <p class="p-false" id="captchaError">验证码错误</p>
                <a href="javascript:;" class="btn-click" id="btn-click-attend">领取红包</a>
            </div>
            <!-- 领取前 -->
            <!-- 领取后 -->
            <div class="hb-wai-after" id="hb-after" style="display:none">
                <h2>红包领取成功</h2>
                <a href="<?php echo $this->config->item('pages_url'); ?>app/download" target="_self" class="btn-click" id="btn-click">立即下载APP（2.35M）</a>
                <h3>仅限手机号<span id="sendPhoneNum1"></span>使用</h3>
            </div>
            <!-- 已领取 -->
            <div class="hb-wai-after" id="hb-already" style="display:none">
                <h2 class="lqg">已领取过红包</h2>         
                <a href="<?php echo $this->config->item('pages_url'); ?>app/download" target="_self" class="btn-click" id="btn-click">立即下载APP（2.35M）</a>
                <h3>仅限手机号<span id="sendPhoneNum2"></span>使用</h3>
            </div>
            <!-- 领取前 -->
            <div class="remarks">
                <h3>每个手机号只能参加一次，详细规则见客户端</h3>
                <p>活动最终解释权归166彩票网所有</p>
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

        // 倒计时60s
        function count(){
            var _this = $('#sendCaptcha');
            var seconds = 59;
            var timer = null;
            _this.off('tap', count);
            _this.html('还剩' + seconds + '秒');
            timer = setInterval(function(){
                if(seconds > 1){
                    seconds -= 1;
                    _this.html('还剩' + seconds + '秒');
                }
                else{
                    clearInterval(timer);
                    _this.html('发送验证码');
                    _this.on('tap', count);
                }
            }, 1000);
        }

        // 刷新图形验证码
        function refreshCaptcha(){
            $('#imgCaptcha').attr('src', '/app/activity/captcha?v=' + Math.random());
        }

        // 发送验证码
        $('#sendCaptcha').on('tap', function(){
            // 手机号码格式检查
            var phone = $('input[name="phone-num"]').val();
            var imgCaptcha = $('input[name="input-txyzm"]').val();

            if(phone == '')
            {
                $.tips({
                    content:'请输入手机号码',
                    stayTime:2000
                });
                return false;
            }

            if(imgCaptcha == '')
            {
                $.tips({
                    content:'请输入图形验证码',
                    stayTime:2000
                });
                return false;
            }

            if( /1\d{10}$/.test(phone) ){
                $.ajax({
                    type: "post",
                    url: '/app/activity/sendCaptcha',
                    data: {phone:phone,imgCaptcha:imgCaptcha},                   
                    success: function (data) {
                        var data = $.parseJSON(data);
                        if(data.status == '1')
                        {
                            count();
                        }
                        else
                        {
                            $.tips({
                                content: data.msg,
                                stayTime: 2000
                            })
                        }
                    },
                    error: function () {
                        $.tips({
                            content: '网络异常，请稍后再试',
                            stayTime: 2000
                        })
                    }
                });
            }
            else
            {
                $.tips({
                    content:'手机号码格式错误',
                    stayTime:2000
                });
                return false;
            }

        }); 

        // 领取红包
        var closeTag = true;
        $('#btn-click-attend').on('tap', function(){
            var phone = $('input[name="phone-num"]').val();
            var imgCaptcha = $('input[name="input-txyzm"]').val();

            try{
                var channel = android.getAppChannel();
            }catch(e){
                var channel = '0';
            }

            if(phone == '')
            {
                $.tips({
                    content:'请输入手机号码',
                    stayTime:2000
                });
                return false;
            }

            if(imgCaptcha == '')
            {
                $.tips({
                    content:'请输入验证码',
                    stayTime:2000
                });
                return false;
            }

            var showLoading = $.loading().loading("mask");

            $.ajax({
                type: "post",
                url: '/app/activity/outerAttend',
                data: {phone:phone,imgCaptcha:imgCaptcha,channel:channel},                   
                success: function (data) {
                    showLoading.loading("hide");
                    var data = $.parseJSON(data);

                    if(data.status == '200')
                    {
                        $('#sendPhoneNum1').html(phone);
                        $('.hb-wai').hide();
                        $('#hb-after').show();
                    }else if(data.status == '300'){
                        closeTag = true;
                        $('input[name="input-txyzm"]').val('');
                        refreshCaptcha();
                        $.tips({
                            content:data.msg,
                            stayTime:2000
                        });
                    }else if(data.status == '400'){
                        $('#sendPhoneNum2').html(phone);
                        $('.hb-wai').hide();
                        $('#hb-already').show();
                    }else
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
        }); 

        // 刷新图形验证码
        $('#change_imgCaptcha').on('tap', function(){
            refreshCaptcha();
        }); 

    });
</script>
<?php $this->load->view('comm/baidu'); ?>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>