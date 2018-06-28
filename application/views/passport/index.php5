<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/login.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript">
$(function() {
    //组件表单交互
    $(".loginFormInput input").each(function () {
        var _this = $(this);
        $(this).focus(function () {
            _this.removeClass().addClass("hover");
            _this.next().hide();
        });
        $(this).blur(function () {
			_this.removeClass("hover");
            if (_this.val() == "") {
                _this.next().show();
            }
        });
    });
    $(".loginFormInput span").each(function () {
        var _this = $(this);
        $(this).click(function () {
            _this.prev("input").focus();
        });
    });

    //Tab切换loginTabWrap
    var $divspan = $("div.loginTab span");
    $divspan.click(function () {
        $(this).addClass("selected").siblings().removeClass("selected");
        var index = $divspan.index(this);
        $("div.loginTabWrap > div").eq(index).show().siblings().hide();
    });
});
$(function() {
    $('.send-vcode').click(function() {
        var $send = $(this);
        var $phonenum = $('#iptMobileRegNumber');
        var phonenum = $.trim($phonenum.val());
        if (!$send.hasClass('loginFormYzmOn') && phoneRegForm.validate($phonenum.get(0))) {
            cx.ajax.post({
                url: cx.url.getPassUrl('query/loginname_exists.do'),
                data: {
                    username: phonenum,
                    logintype: 1
                },
                success: function(response) {
                    if (response.code == 1170) {
                        phoneRegForm.renderTip('该手机号已注册');
                        return;
                    }
                    cx.ajax.post({
                        url: cx.url.getPassUrl('sendMsg.do'),
                        data: {
                            msgType: '0',
                            paras: [''],
                            mobile: phonenum,
                            uid: 0,
                            isJson: 1
                        },
                        success: function(response) {
                            if (response.code == 0) {
                                $send.addClass('loginFormYzmOn');
                                var counter = new cx.Counter({
                                    start: 90,
                                    step: 1
                                });
                                counter.countDown(function(tick) {
                                    $send.html('剩余' + tick + '秒');
                                }, function() {
                                    $send.removeClass('loginFormYzmOn').html('发送验证码');
                                });
                                phoneRegForm.renderTip('短信已发送至手机' + phonenum + ' 请查收');
                            }
                        }
                    });
                }
            });
        }
    });
    var redirect = '<?php echo $redirect; ?>';
    var loginForm = new cx.vform('.login-form', {
        submit: function(data) {
            var self = this;
            data.from = 'pcweb';
            $.ajax({
                type: 'post',
                url: baseUrl + 'passport/login',
                data: data,
                success: function(response) {
                    if (response.code == 0) {
                        location.href = baseUrl + redirect;
                    } else {
                        if (response.code == 16) {
                            $('.old-name').val(data.username);
                            $('.old-pwd').val(data.password);
                            $('.old-form').submit();
                        }
                        self.renderTip(response.msg);
                    }
                }
            });
        }
    });
    var phoneRegForm = new cx.vform('.phone-reg-form', {
        submit: function(data) {
            var self = this;
            $.ajax({
                type: 'post',
                url: baseUrl + 'passport/phoneRegister',
                data: data,
                success: function(response) {
                    if (response.code == 0) {
                        location.href = baseUrl + redirect;
                    } else {
                        self.renderTip(response.msg);
                    }
                }
            });
        }
    });
    var userRegForm = new cx.vform('.username-reg-form', {
        submit: function(data) {
            var self = this;
            $.ajax({
                type: 'post',
                url: baseUrl + 'passport/usernameRegister',
                data: data,
                success: function(response) {
                    if (response.code == 0) {
                        location.href = baseUrl + redirect;
                    } else {
                        self.renderTip(response.msg);
                    }
                }
            });
        }
    });
    cx.ajax.get({
        url: cx.url.getBusiUrl('ticket/msg/allprize'),
        success: function(response) {
            if (response.code == 0) {
                $('.all-prize').html(response.data[0]);
            }
        }
    })
});
</script>
<div class="AccountWrap">
  <!--容器-->
  <div class="loginWrap clearfix">
    <!--fl-->
    <div class="fl">
      <h4>平台累计中奖奖金：</h4>
      <h2 id="h2TotalPrize"><span class="all-prize"></span><strong>元</strong></h2>
    </div>
    <!--fl end-->
    <!--fr-->
    <div class="fr">
        <div class="login-container">
            <div class="loginTab">
                <span class="<?php if (!$target): ?>selected<?php endif; ?>">登录</span>
                <span class="<?php if ($target): ?>selected<?php endif; ?>">手机注册</span> <span>2345账号注册</span>
            </div>
            <div class="clear"></div>
            <div class="loginTabWrap">
                <!--登录-->
                <div style="<?php if ($target): ?>display: none;<?php endif; ?>">
                  <div class="loginForm login-form">
                    <p class="loginFormInput">
                      <input id="iptLoginUserName" class="vcontent" data-rule="username_phonenum" name="username" type="text" />
                      <span>2345账号/手机号/邮箱</span></p>
                    <p class="loginFormInput">
                      <input id="iptLoginPassword" class="vcontent" data-rule="password" name="password" type="password" />
                      <span>输入登录密码</span></p>
                    <p id="pLoginErrorHint" class="loginFormError tip" style="display: none;"></p>
                    <p class="loginFormBtn"> <a class="submit" id="passport_login">登 录</a></p>
                    <p class="loginFormForget"> <a href="<?php echo $baseUrl; ?>passport/findPwd">忘记密码？</a></p>
                    <div class="other">
                      <h3>其他登录方式</h3>
                      <ul>
                        <li class="souhu"><a href="<?php echo SOHU_PASS; ?>&redirect_uri=http://sohu.51caixiang.com/passport/thirdLogin?app=SoHu"></a></li>
                        <li class="qq"><a></a></li>
                        <li class="weixin"><a></a></li>
                        <li class="weibo"><a></a></li>
                        <li class="pay"><a></a></li>
                      </ul>
                    </div>
                    <div class="clear"></div>
                  </div>
                </div>
                <!--手机注册-->
                <div style="<?php if (!$target): ?>display: none;<?php endif; ?>">
                  <div class="loginForm phone-reg-form">
                    <p class="loginFormInput">
                      <input id="iptMobileRegNumber" class="vcontent" data-rule="phonenum" name="username" type="text" />
                      <span>手机号</span></p>
                    <p class="loginFormInput">
                      <input id="iptMobileRegCode" class="vcontent" data-rule="checkcode" type="text" name="phonecheckcode" style="width: 200px" />
                      <span>短信验证码</span> <a id="aSendCheckCode" class="loginFormYzm send-vcode">发送验证码</a></p>
                    <p class="loginFormInput">
                      <input id="iptMobileRegPassword" class="vcontent" data-rule="password" name="password" type="password" />
                      <span>密码</span></p>
                    <p class="loginFormInput">
                      <input id="iptMobileRegConfirmPassword" class="vcontent" data-rule="same" data-with="password" type="password" />
                      <span>确认密码</span></p>
                    <p class="loginFormAgreement">
                      <input id="iptMobileRegAgree" type="checkbox" checked="checked" class="vcontent" />
                      我已阅读，并同意<a href="<?php echo $baseUrl; ?>help" target="_blank">《用户协议》</a></p>
                    <p id="pMobileRegErrorHint" class="loginFormError tip" style="display: none;"></p>
                    <p class="loginFormBtn"> <a id="passport_sjzc" class="submit">注 册</a> </p>
                  </div>
                </div>
                <!--2345账号注册-->
                <div style="display: none">
                  <div class="loginForm username-reg-form">
                    <p class="loginFormInput">
                      <input id="iptUserNameRegUserName" class="vcontent" data-rule="username" name="username" type="text" />
                      <span>2345账号</span></p>
                    <p class="loginFormInput">
                      <input id="iptUserNameRegPassword" class="vcontent" type="password" data-rule="password" name="password" />
                      <span>密码</span></p>
                    <p class="loginFormInput">
                      <input id="iptUserNameRegConfirmPassword" class="vcontent" type="password" data-rule="same" data-with="password" />
                      <span>确认密码</span></p>
                    <p class="loginFormAgreement">
                      <input id="iptUserNameRegAgree" class="vcontent" checked="checked" type="checkbox" />
                      我已阅读，并同意<a href="<?php echo $baseUrl; ?>help" target="_blank">《用户协议》</a></p>
                    <p id="pUserNameRegErrorHint" class="loginFormError tip" style="display: none;"></p>
                    <p class="loginFormBtn"> <a class="submit" id="passport_yhmzc">注 册</a></p>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <!--fr end-->
  </div>
  <!--容器end-->
</div>
<form style="display: none;" action="<?php echo $baseUrl; ?>migrate" method="post" class="old-form">
  <input name="username" class="old-name" value="" type="hidden" />
  <input name="password" class="old-pwd" value="" type="hidden" />
</form>
