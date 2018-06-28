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
    <title>新用户免费领彩票</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/wx-newuser.min.css')?>">
</head>
<body ontouchstart="">
    <div class="wrap">
        <div class="wrap-content">
            <!-- 领取前 -->
            <div class="dkw-form" id="send-before" style="display: block;">
                <div class="dkw-form-item">
                    <input type="tel" placeholder="输入手机号，领取红包" name="phone-num">
                </div>
                <p class="p-false">手机号错误</p>
                <div class="dkw-form-item yzm">
                    <input type="tel" placeholder="输入4位验证码" name="input-txyzm" class="input-yzm">
                    <img id="imgCaptcha" src="/app/activity/captcha/1/newUserCaptcha" alt="">
                    <a href="javascript:;" class="change-img">换一张</a>
                </div>
                <p class="p-false">验证码错误</p>
                <a href="javascript:;" class="btn-click sendSms" id="btn-click">领取彩票红包</a>
            </div>
            <!-- 领取前 -->
            <!-- 领取后 -->
            <div class="dkw-form dkw-result" id="send-after" style="display: none;">
                <h2 id="after-msg">红包领取成功</h2>
                <p class="tips-txt">短信已发送至手机，<br>您可点击短信中的链接下载APP</p>
                <a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.caipiao166&ckey=CK1357914765625" id="btn-click" class="btn-click">立即下载APP使用</a>
                <!-- 按钮禁用样式添加类名 btn-click-dis -->
                <p class="tips-txt2">仅限手机号<span id="phoneTxt">-----</span>使用</p>
            </div>
            <!-- 领取前 -->
        </div>
        <div class="plus-rule">
            <ol class="rule-overflow-y">
                <li>1、活动时间：2017年03月10日 00:00:00 - 2017年04月01日 23:59:59；</li>
                <li>2、本次活动为166彩票新用户专享，验证手机号后即可领取红包，每位用户限领一次；</li>
                <li>
                    3、188元彩票红包价值如下：
                    <ol>
                        <li>a. 3元彩票（注册实名即可使用）</li>
                        <li>b. 2元红包（充值20元及以上可用），5个</li>
                        <li>c. 5元红包（充值50元及以上可用），5个</li>
                        <li>d. 10元红包（充值100元及以上可用），5个</li>
                        <li>e. 20元红包（充值200元及以上可用），5个</li>
                    </ol>
                </li>
                <li>4、红包有效期为30天，逾期未使用的红包将被系统收回；</li>
                <li>5、活动充值与赠送的红包均不能提现，只能用于购彩，中奖奖金可提现；</li>
                <li>6、活动过程中如用户通过不正当手段领取彩金，166彩票网有权不予赠送、限制提现、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权；</li>
                <li>7、关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
            </ol>
            <div class="rule-arrow">规则</div>
        </div>
        <div class="rule-bg"></div>
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

        var psHeight = $('.plus-rule').height();
        $('.plus-rule').css({
            'top': '-' + psHeight + 'px'
        });
        setTimeout(function () {
            $('.plus-rule').css({
                'transition': 'all ease-in 400ms',
                '-webkit-transition': 'all ease-in 400ms'
            })
        }, 2000)
        $('.plus-rule').on('click', '.rule-arrow', function() {
            var thisParent = $(this).parents('.plus-rule');
            thisParent.toggleClass('plus-rule-show');
            if(!thisParent.hasClass('plus-rule-show')) {
                $('.plus-rule').css({
                    'top': '-' + psHeight + 'px'
                })
            } else {
                $('.plus-rule').css({
                    'top': 0
                })
            }
            $('body').toggleClass('overflowScroll');
            return false;
        })
        $('.plus-rule + .rule-bg').on('click', function () {
            $('.plus-rule').css({
                'top': '-' + psHeight + 'px'
            })
            $('.plus-rule').removeClass('plus-rule-show');
            $('body').removeClass('overflowScroll');
        })

        // 换一张图片
        var timer = null;
        $('.change-img').on('click', function () {
            var self = $(this);
            clearTimeout(timer);
            self.addClass('change-rotate')
            timer = setTimeout(function () {
                self.removeClass('change-rotate');
                refreshCaptcha();
            }, 400)
            return false;
        })

        // 领取
        $('.sendSms').on('click', function () {
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
                    url: '/app/activity/sendEventSms/newUser',
                    data: {phone:phone,imgCaptcha:imgCaptcha},                   
                    success: function (data) {
                        var data = $.parseJSON(data);
                        if(data.status == '1'){
                            $('#after-msg').html('红包领取成功');
                            $('#phoneTxt').html(data.data);
                            $('#send-before').hide();
                            $('#send-after').show();
                        }
                        else if(data.status == '2'){
                            $('#after-msg').html('已领取过红包');
                            $('.tips-txt').hide();
                            $('#send-before').hide();
                            $('#send-after').show();
                            $('#phoneTxt').html(data.data);
                        }
                        else
                        {
                            $.tips({
                                content: data.msg,
                                stayTime: 2000
                            })
                        }
                        refreshCaptcha()
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
        })

        function refreshCaptcha()
        {
            $('#imgCaptcha').attr('src', '/app/activity/captcha/1/newUserCaptcha?v=' + Math.random());
        }
    });
</script>
<?php $this->load->view('comm/baidu'); ?>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>