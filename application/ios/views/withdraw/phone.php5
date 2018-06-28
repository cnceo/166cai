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
    <title>身份验证</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/withdraw-money.min.css');?>">
</head>
<body>
    <div class="wrapper">
        <form class="form-hack" action="">
            <div class="cp-box cp-box-b">
                <div class="cp-box-hd">
                    <h2 class="cp-box-title">通过手机号码校验身份</h2>
                </div>
                <div class="cp-box-bd">
                    <ul class="cp-list cp-list-senior">
                        <li>
                            <div class="cp-form-item ipt-tel cp-list-txt">
                                <label for="telNum">手机号码</label>
                                <input id="telNum" name="phone" type="tel" value="<?php echo substr_replace($phone, '****', 3, 4);?>" readonly>
                                <a href="javascript:;">发送验证码</a>
                            </div>
                        </li> 
                        <li>
                            <div class="cp-form-item cp-list-txt">
                                <label for="vCode">验证码</label>
                                <input id="vCode" name="vCode" type="tel" placeholder="请输入收到的验证码" maxlength="6">
                            </div>
                        </li> 
                    </ul>
                </div>
            </div>    
            <div class="btn-group">
               <a id="applyWithdraw2" class="btn btn-block-confirm btn-recharge" href="javascript:void(0);">确认</a>
            </div>
            <input type='hidden' class='' name='moneyNum' value='<?php echo $moneyNum;?>'/>
            <input type='hidden' class='' name='token' value='<?php echo $token;?>'/>
            <input type='hidden' class='' name='bankId' value='<?php echo $bankId;?>'/>
            <input type='hidden' class='' name='action' value='2'/>
        </form>
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

            // 虚拟键盘 hack
            $('.form-hack').on('submit', function(){
                $('#applyWithdraw2').trigger('tap');
                return false;
            })

            // 发送验证码-倒计时60s
            function count(_this){
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

            var countTag = $('.ipt-tel a');
            var telTag = true;
            // 发送短信验证码
            $('.ipt-tel').find('a').on('tap', function(){
                if(telTag){
                    telTag = false;
                    $.ajax({
                        type: 'post',
                        url: '/ios/withdraw/sendPhone',
                        data: {},
                        success: function (response) {
                            var response = $.parseJSON(response);
                            if(response.status == '1')
                            {
                                telTag = true;

                                count(countTag);
                                $.tips({
                                    content: response.msg,
                                    stayTime: 2000
                                })
                            }else{
                                telTag = true;
                                $.tips({
                                    content: response.msg,
                                    stayTime: 2000
                                })
                            }
                        },
                        error: function () {
                            telTag = true;
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                }
            });

            var closeTag = true;
            
            // 提现申请
            $('#applyWithdraw2').on('click', function(){
                // 失焦
                $('#newPsw').blur();

                var moneyNum = $('input[name="moneyNum"]').val();
                var token = $('input[name="token"]').val();
                var bankId = $('input[name="bankId"]').val();
                var action = $('input[name="action"]').val();
                var captcha = $('input[name="vCode"]').val();              

                if(captcha == '')
                {
                    $.tips({
                        content: '请输入验证码',
                        stayTime: 2000
                    })
                    return false;
                }

                var reg = /^\+?([0-9]+|[0-9]+\.[0-9]{0,2})$/;
                if(!reg.test(moneyNum))
                {
                    $.tips({
                        content: '请输入有效的提现金额',
                        stayTime: 2000
                    })
                    return false;
                }

                if(moneyNum <= 0)
                {
                    $.tips({
                        content: '请输入有效的提现金额',
                        stayTime: 2000
                    })
                    return false;
                }

                if(closeTag)
                {
                    closeTag = false;
                    var showLoading = $.loading().loading("mask");
                    $.ajax({
                        type: 'post',
                        url: '/ios/withdraw/applyWithdraw',
                        data: {moneyNum:moneyNum,token:token,bankId:bankId,captcha:captcha,action:action},

                        success: function (response) {
                            showLoading.loading("hide");
                            var response = $.parseJSON(response);
                            if(response.status == 2)
                            {
                                closeTag = true;
                                window.location.href = response.data;
                            }else{
                                closeTag = true;
                                $.tips({
                                    content: response.msg,
                                    stayTime: 2000
                                })
                            }
                        },
                        error: function () {
                            showLoading.loading("hide");
                            closeTag = true;
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                }
            });

        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>