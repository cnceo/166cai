<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection"> 
    <meta content="email=no" name="format-detection">
    <title>身份验证</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/login.min.css');?>">
</head>
<body>
    <div class="wrapper login-find">
        <form class="form-hack" action="">
            <div class="cp-form-item ipt-psw cp-box-login">
                <label for="newPsw">支付密码</label>
                <input id="newPsw" name="newPsw" type="password" placeholder="请输入支付密码" maxlength="16">
                <a href="javascript:;"></a>
            </div>
            <div class="btn-group">
               <a id="applyWithdraw2" class="btn btn-block-confirm btn-recharge" href="javascript:void(0);">确认</a>
            </div>
            <input type='hidden' class='' name='moneyNum' value='<?php echo $moneyNum;?>'/>
            <input type='hidden' class='' name='token' value='<?php echo $token;?>'/>
            <input type='hidden' class='' name='bankId' value='<?php echo $bankId;?>'/>
            <input type='hidden' class='' name='action' value='2'/>
            <input type='hidden' class='' name='encrypt' value='payPsw|'/>
        </form>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/BigInt.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/Barrett.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/RSA.js');?>" type="text/javascript"></script>
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

            $(function(){
                window.cx || (window.cx = {});
                cx.pub_salt = '<?php echo $this->pub_salt?>';
                cx.rsa_encrypt = function( val ) {
                    var rsa_n = 'B31FD13CCDA7684626351A49159B9FDD';        
                    setMaxDigits(131);
                    var key = new RSAKeyPair("10001", '', rsa_n);
                    return encryptedString(key, val + '<PSALT>' + cx.pub_salt);
                }
            });

            // 虚拟键盘 hack
            $('.form-hack').on('submit', function(){
                $('#applyWithdraw2').trigger('tap');
                return false;
            })
            
            //显示隐藏密码
            $('.ipt-psw').find('a').on('click', function(){
                var aPsw = $(this).parents('.ipt-psw').find('input');
                if(aPsw.attr('type') == 'password'){
                    aPsw.attr('type', 'text');
                }else if(aPsw.attr('type') == 'text'){
                    aPsw.attr('type', 'password');
                }
            })

            var closeTag = true;
            
            // 提现申请
            $('#applyWithdraw2').on('click', function(){
                // 失焦
                $('#newPsw').blur();

                var moneyNum = $('input[name="moneyNum"]').val();
                var token = $('input[name="token"]').val();
                var bankId = $('input[name="bankId"]').val();
                var action = $('input[name="action"]').val();
                var payPsw = $('input[name="newPsw"]').val();
                var encrypt = $('input[name="encrypt"]').val();
                try{
                    var app_version = android.getAppVersion();
                    var channel = android.getAppChannel();
                }catch(e){
                    var app_version = '';
                    var channel = '';
                }
                

                if(payPsw == '')
                {
                    $.tips({
                        content: '请输入支付密码',
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

                payPsw = cx.rsa_encrypt(payPsw);
                if(closeTag)
                {
                    closeTag = false;
                    var showLoading = $.loading().loading("mask");
                    $.ajax({
                        type: 'post',
                        url: '/app/withdraw/applyWithdraw',
                        data: {moneyNum:moneyNum,token:token,bankId:bankId,payPsw:payPsw,action:action,encrypt:encrypt,app_version:app_version,channel:channel},
                        // beforeSend: loading,
                        success: function (response) {
                            showLoading.loading("hide");
                            var response = $.parseJSON(response);
                            if(response.status == 2)
                            {
                                // 跳转至充值记录页
                                // console.log(response.data);
                                closeTag = true;
                                window.location.href = response.data;
                                // $.tips({
                                //     content: response.msg,
                                //     stayTime: 2000
                                // })
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