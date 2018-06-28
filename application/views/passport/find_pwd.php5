<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/findPwd.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript">
$(function(){
	//组件表单交互
	$(".loginFormInput input").each(function(){
		var _this = $(this);
		$(this).focus(function(){
			_this.removeClass().addClass("hover");
			_this.next().hide();
		});
		$(this).blur(function(){
			if(_this.val() == ""){
				_this.removeClass();
				_this.next().show();
			}
		});
	});
	$(".loginFormInput span").each(function(){
		var _this = $(this);
		$(this).click(function(){
			_this.prev("input").focus();
		});
	});
	var findForm = new cx.vform('.find-form', {
		submit: function(data) {
			var self = this;
            cx.ajax.post({
                url: cx.url.getPassUrl('update/forgetpassword.do'),
                data: data,
                success: function(response) {
                    if (response.code == 0) {
                        cx.Alert({
                        	content: '修改成功',
                            confirmCb: function() {
                                location.href = baseUrl + 'account';
                            }
                        });
                    } else {
                        self.renderTip(response.msg);
                    }
                }
            });
		}
	});
	$('.send-vcode').click(function() {
        var $send = $(this);
        var $phonenum = $('#phonenum');
        var phonenum = $.trim($phonenum.val());
        if (!$send.hasClass('loginFormYzmOn') && findForm.validate($phonenum.get(0))) {
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
                        findForm.renderTip('短信已发送至手机' + phonenum + ' 请查收');
                    }
                }
            });
        }
    });
});
</script>
<div class="AccountWrap">
    <!--容器-->
    <div class="loginWrap clearfix">
        <div class="fl">
            <h3>客服电话:</h3>
            <h2 id="h2TotalPrize"><font color="#0e63a6">400-096-5100</font></h2>
        </div>
        <div class="fr">
            <div class="login-container">
                 <div class="loginTab">
                    <span>重置登录密码</span>
                </div>
                <div class="loginForm find-form">
                    <p class="loginFormInput">
                        <input type="text" data-rule="phonenum" id="phonenum" class="vcontent" name="phone" />
                        <span>手机号</span>
                    </p>
                    <p class="loginFormInput">
                        <input type="text" style="width:200px" data-rule="checkcode" class="vcontent" name="phonecheckcode" />
                        <span>短信验证码</span>
                        <a class="loginFormYzm send-vcode">发送验证码</a>
                    </p>
                    <p class="loginFormInput">
                        <input type="password" data-rule="password" class="vcontent" name="newpassword" />
                        <span>新密码</span>
                    </p>
                    <p class="loginFormInput">
                        <input type="password" data-rule="same" data-with="newpassword" class="vcontent" type="password" />
                        <span>确认密码</span>
                    </p>
                    <p class="loginFormError tip" style="display: none;"></p>
                    <p class="loginFormBtn">
                        <a class="submit">完 成</a>
                    </p>
                </div>
            </div>   
        </div>
    </div>
    <!--容器end-->
</div>
