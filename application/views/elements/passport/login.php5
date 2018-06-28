<style>
    .loginPopWrap {
        border: 1px solid #ccc;
        position: fixed;
        left: 50%;
        top: 10%;
        margin-left: -175px;
        background: #fff;
        z-index: 60;
        display: none;
    }
    .loginPopWrap .title {
        height: 50px;
        line-height: 50px;
        text-indent: 10px;
        width: 100%;
        background: #c9141b;
        font-size: 20px;
        color: #fff;
        position: relative;
    }
    .loginPopWrap .title .close {
        position: absolute;
        top: 13px;
        right: 15px;
    }
    .loginPopWrap .loginForm{ padding:15px; width:350px; height:334px; position:relative;}
    .loginPopWrap .loginFormInput{ padding:6px 0; position:relative; height:46px;}
    .loginPopWrap .loginFormInput input{ width:343px; height:46px; line-height:46px; border:1px solid #c4c9cd; background-color:#f6f9fd; position:absolute; top:0; left:0; z-index:11; border-radius:3px; font-size:14px;}
    .loginPopWrap .loginFormInput span{ color:#c2cdd9; font-size:14px; width:200px; padding-left:10px; position:absolute; top:0; left:0; z-index:22; line-height:46px;}
    .loginPopWrap .loginFormInput input.hover{ border:1px solid #368ae8; background:#FCFCFC;}
    .loginPopWrap .loginFormYzm{ display:inline-block; width:110px; height:46px; line-height:46px; color:#fff; background:#251C1D; border-radius:2px; position:absolute; top:0; left:240px; font-size:14px; text-align:center;}
    .loginPopWrap .loginFormYzmOn{ background-position:0 -63px;}
    .loginPopWrap .loginFormInput a:hover{ text-decoration:none;}
    .loginPopWrap .loginFormForget{ height:25px; line-height:25px; text-align:center; width:350px;}
    .loginPopWrap .loginFormForget a{ color:#5dc0e7; font-size:16px;}
    .loginPopWrap .loginFormError{ width:350px; height:35px; line-height:35px; text-align:center; font-size:14px; font-weight:bold; background:#f6e4e4; border-radius:3px;}
    .loginPopWrap .loginFormBtn{ padding:5px 0;}
    .loginPopWrap .loginFormBtn a{ width:350px; height:45px; line-height:45px; text-align:center; color:#fff; font-size:16px; background:#368AE8; border-radius:2px; display:block;}
    .loginPopWrap .loginFormBtn a:hover{ text-decoration:none;}
    .loginPopWrap .loginPartner{ height:105px; width:100%; background:#251c1d; line-height:105px; text-align:center;}
    .loginPopWrap .loginPartner a{ margin:0 25px;}

    .loginPopWrap .other{ overflow:hidden; position:absolute; bottom:5px;}
    .loginPopWrap .other h3{ font-size:14px;}
    .loginPopWrap .other ul{ width:350px; margin-top:10px; }
    .loginPopWrap .other ul li{ float:left; margin-left:15px;}
    .loginPopWrap .other ul li a{ display:block; width:50px; height:70px;}
    .loginPopWrap .other ul .souhu a{ background:url(/caipiaoimg/v1.0/images/login/sohu.png) no-repeat;}
    .loginPopWrap .other ul .qq a{ background:url(/caipiaoimg/v1.0/images/login/qq.png) no-repeat;}
    .loginPopWrap .other ul .weixin a{ background:url(/caipiaoimg/v1.0/images/login/weixin.png) no-repeat;}
    .loginPopWrap .other ul .weibo a{ background:url(/caipiaoimg/v1.0/images/login/weibo.png) no-repeat;}
    .loginPopWrap .other ul .pay a{ background:url(/caipiaoimg/v1.0/images/login/pay.png) no-repeat;}
</style>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script>
$(function() {
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

    cx.PopLogin = (function() {
        var me = {};
        var $wrapper = $('.loginPopWrap');

        $wrapper.find('.close').click(function() {
            $wrapper.hide();
            cx.Mask.hide();
        });

        me.show = function() {
            cx.Mask.show();
            $wrapper.show();
        };

        me.hide = function() {
            $wrapper.hide();
            cx.Mask.hide();
        };

        return me;
    })();

    var loginForm = new cx.vform('.pop-login-form', {
        submit: function(data) {
            var self = this;
            data.from = 'pcweb';
            $.ajax({
                type: 'post',
                url: baseUrl + 'passport/login',
                data: data,
                success: function(response) {
                    if (response.code == 0) {
                        cx.PopLogin.hide();
                        $('.not-login').removeClass('not-login');
                        $('.login').attr('href', baseUrl + 'passport/logout').html('退出');
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
});

</script>
<div class="loginPopWrap">
    <h1 class="title">登录 <a class="close"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/close.gif');?>" alt="关闭" width="22" height="22"></a></h1>
    <div class="loginForm pop-login-form">
        <p class="loginFormInput">
            <input id="iptLoginUserName" class="vcontent" data-rule="username_phonenum" name="username" type="text" />
            <span>2345账号/手机号/邮箱</span></p>
        <p class="loginFormInput">
            <input id="iptLoginPassword" class="vcontent" data-rule="password" name="password" type="password" />
            <span>输入登录密码</span></p>
        <p id="pLoginErrorHint" class="loginFormError tip" style="display: none;"></p>
        <p class="loginFormBtn"> <a class="submit" id="passport_login">登 录</a></p>
        <p class="loginFormForget"> <a href="http://sohu.51caixiang.com/passport/findPwd">忘记密码？</a></p>
        <div class="other">
            <h3>其他登录方式</h3>
            <ul>
                <li class="souhu"><a href="<?php echo SOHU_PASS; ?>&redirect_uri=<?php echo $baseUrl; ?>passport/thirdLogin?app=SoHu"></a></li>
                <li class="qq"><a></a></li>
                <li class="weixin"><a></a></li>
                <li class="weibo"><a></a></li>
                <li class="pay"><a></a></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>
