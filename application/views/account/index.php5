<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/myLottery.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<style type="text/css">
.submit {
	display: none;
}
.cancel {
	display: none;
}
.unbind {
	display: none;
}
.vcontent.readonly {
	border: none;
}
.helper {
	display: none;
}
.userAccountTable td span.tip.hidden {
	display: none;
}

.userAccountTable td input.username,
.userAccountTable td input.nickname,
.userAccountTable td input.email,
.userAccountTable td input.identification {
	width: 100%;
}
</style>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript">
$(function() {
	$('.send-vcode').click(function() {
        var $send = $(this);
        var $phonenum = $('#phonenum');
        var phonenum = $.trim($phonenum.val());
        if (!$send.hasClass('loginFormYzmOn') && phonenumForm.validate($phonenum.get(0))) {
            cx.ajax.post({
                url: cx.url.getPassUrl('query/loginname_exists.do'),
                data: {
                    username: phonenum,
                    logintype: 1
                },
                success: function(response) {
                    if (response.code == 1170) {
                        phonenumForm.renderTip('该手机号已注册');
                        return;
                    }
                    cx.ajax.post({
                        url: cx.url.getPassUrl('sendMsg.do'),
                        data: {
                            msgType: '0',
                            paras: [''],
                            mobile: phonenum,
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
                                phonenumForm.renderTip('短信已发送至手机' + phonenum + ' 请查收');
                            }
                        }
                    });
                }
            });
        }
    });
	$('.modify').click(function() {
		var $this = $(this);
		var $vcontent = $this.closest('tr').find('.vcontent');
		$this.closest('tr').find('.helper').show();
		$vcontent.attr('readonly', false).removeClass('readonly').trigger('focus');
		$vcontent.data('old_val', $vcontent.val());
		$this.hide().siblings().show();
	});
	$('.cancel').click(function() {
		var $this = $(this);
		var $tr = $this.closest('tr');
		var $vcontent = $tr.find('.vcontent');
		$vcontent.not('.helper').attr('readonly', true).addClass('readonly').val($vcontent.data('old_val'));
		$tr.find('.modify').show();
		$tr.find('.submit').hide();
		$tr.find('.unbind').hide();
		$tr.find('.tip').addClass('hidden').hide();
		$tr.find('.helper').hide();
		$this.hide();
	});
	var usernameForm = new cx.vform('.username-form', {
		submit: function(data) {
			var self = this;
			data.isToken = 1;
			cx.ajax.post({
				url: cx.url.getPassUrl('update/username.do'),
				data: data,
				success: function(response) {
					callback(response, self);
				}
			});
		}
	});
	var phonenumForm = new cx.vform('.phone-form', {
		submit: function(data) {
			var self = this;
			data.isToken = 1;
			cx.ajax.post({
				url: cx.url.getPassUrl('update/phone_bound.do'),
				data: data,
				success: function(response) {
					callback(response, self);
				}
			});
		}
	});
	var displayForm = new cx.vform('.display-form', {
		submit: function(data) {
			var self = this;
			data.isToken = 1;
			cx.ajax.post({
				url: cx.url.getPassUrl('update/displayname.do'),
				data: data,
				success: function(response) {
					callback(response, self);
				}
			});
		}
	});
    var emailForm = new cx.vform('.email-form', {
        submit: function(data) {
            var self = this;
            data.isToken = 1;
			cx.ajax.post({
				url: cx.url.getPassUrl('update/email_bound.do'),
				data: data,
				success: function(response) {
					callback(response, self);
				}
			});
        }
    });
    var idenForm = new cx.vform('.iden-form', {
        submit: function(data) {
            var self = this;
            data.isToken = 1;
			cx.ajax.post({
				url: cx.url.getPassUrl('update/idcardbound.do'),
				data: data,
				success: function(response) {
					callback(response, self);
				}
			});
        }
    });
	function callback(response, content) {
		if (response.code == 0) {
			location.href = location.href;
			var $tr = content.$submit.closest('tr');
			$tr.find('.vcontent').attr('readonly', true).addClass('readonly');
			$tr.find('.modify').show();
			$tr.find('.cancel').hide();
			$tr.find('.helper').hide();
			$tr.find('.unbind').hide();
			$tr.find('.tip').addClass('hidden');
			content.$submit.hide();
		} else {
			content.renderTip(response.msg);
		}
	}
	$('.unbind-phone').click(function() {
		new cx.Confirm({
			single: '确定解绑手机号？',
			confirmCb: function() {
				cx.ajax.post({
					url: cx.url.getPassUrl('update/phone_unbound.do'),
					data: {
						isToken: 1
					},
					success: function(response) {
						if (response.code == 0) {
							cx.Alert({
								content: '解绑成功',
								confirmCb: function() {
									location.href = location.href;
								}
							});
						} else {
							phonenumForm.renderTip(response.msg);
						}
					}
				});
			}
		});
	});
	$('.unbind-email').click(function() {
		new cx.Confirm({
			single: '确定解绑邮箱？',
			confirmCb: function() {
				cx.ajax.post({
					url: cx.url.getPassUrl('update/email_unbound.do'),
					data: {
						isToken: 1
					},
					success: function(response) {
						if (response.code == 0) {
							cx.Alert({
								content: '解绑成功',
								confirmCb: function() {
									location.href = location.href;
								}
							});
						} else {
							emailForm.renderTip(response.msg);
						}
					}
				});
			}
		});

	});
	$('.unbind-id').click(function() {
		new cx.Confirm({
			single: '确定解绑身份证号？',
			confirmCb: function() {
				cx.ajax.post({
					url: cx.url.getPassUrl('update/idcard_unbound.do'),
					data: {
						isToken: 1
					},
					success: function(response) {
						if (response.code == 0) {
							cx.Alert({
								content: '解绑成功',
								confirmCb: function() {
									location.href = location.href;
								}
							});
						} else {
							idenForm.renderTip(response.msg);
						}
					}
				});
			}
		});
	});
});
</script>
<!--容器-->
<div class="wrap clearfix">
		<!--个人信息-->
		<?php $this->load->view('elements/account/basic_info'); ?>
		<!--个人信息end-->

		<!--彩票-->
		<div class="userLottery">
			<div class="userLotteryTab">
					<div class="fl">
							<a href="<?php echo $baseUrl; ?>orders">彩票</a>
							<a href="<?php echo $baseUrl; ?>bills">账单</a>
							<a href="<?php echo $baseUrl; ?>account" class="selected">账户信息</a>
					</div>
			</div>
			<div class="userLotteryBox clearfix">
				<div class="userAccount">
					<table class="userAccountTable">
						<tr class="username-form">
							<th width="91">2345账号：</th>
							<td width="417">
								<input type="text" data-rule="username" name="username" class="vcontent username readonly" readonly value="<?php echo $account['username']; ?>" />
							</td>
							<td width="150">
								<?php if (empty($account['username'])): ?>
								<a class="wordRed modify">修改</a>
								<a class="wordRed submit">保存</a>
								<a class="wordGray cancel">取消</a>
								<?php endif; ?>
							</td>
							<td><span class="accountError tip hidden"></span></td>
						</tr>
                        <tr class="pwd-form">
							<th>密码：</th>
                            <td>******</td>
							<td>
								<?php if (!empty($account['mobile'])): ?>
                                <a class="wordRed" href="<?php echo $baseUrl; ?>passport/findPwd">修改</a>
                                <?php else: ?>
                                请先绑定手机号
                                <?php endif; ?>
							</td>
							<td><span class="accountError tip hidden"></span></td>
                        </tr>
						<tr class="display-form">
							<th>昵称：</th>
							<td>
								<input type="text" class="vcontent nickname readonly" data-rule="nickname" name="displayname" readonly value="<?php echo $account['displayname']; ?>" />
							</td>
							<td>
								<a class="wordRed modify">修改</a>
								<a class="wordRed submit">保存</a>
								<a class="wordGray cancel">取消</a>
							</td>
							<td><span class="accountError tip hidden"></span></td>
						</tr>
						<tr class="phone-form">
							<th>手机：</th>
							<td>
								<input type="text" class="vcontent readonly" data-rule="phonenum" id="phonenum" name="phone" readonly value="<?php echo $account['mobile']; ?>" />
								<input type="hidden" class="vcontent readonly" name="username" value="<?php echo $account['username']; ?>" />
								<span class="userFormYzm helper send-vcode">发送验证码</span>
								<input type="text" class="helper vcontent" style="width:75px" data-rule="checkcode" name="phonecheckcode" />
							</td>
							<td>
								<a class="wordRed modify">修改</a>
								<a class="wordRed submit">保存</a>
								<a class="wordGray cancel ">取消</a>
								<?php if (!empty($account['mobile'])): ?>
								<a class="wordGray unbind unbind-phone">解绑</a>
								<?php endif; ?>
							</td>
							<td><span class="accountError tip hidden"></span></td>
						</tr>
						<tr class="email-form">
							<th>邮箱：</th>
							<td><input type="text" class="vcontent email readonly" data-rule="email" name="email" readonly value="<?php echo $account['email']; ?>" /></td>
							<td>
								<a class="wordRed modify">修改</a>
								<a class="wordRed submit">保存</a>
								<a class="wordGray cancel">取消</a>
								<?php if (!empty($account['email'])): ?>
								<a class="wordGray unbind unbind-email">解绑</a>
								<?php endif; ?>
							</td>
							<td><span class="accountError tip hidden"></span></td>
						</tr>
						<tr class="iden-form">
							<th>身份证：</th>
							<td><input type="text" class="vcontent identification readonly" data-rule="identification" name="newidcard" readonly value="<?php echo $account['identification']; ?>" /></td>
							<td>
								<a class="wordRed modify">修改</a>
								<a class="wordRed submit">保存</a>
								<a class="wordGray cancel">取消</a>
								<?php if (!empty($account['identification'])): ?>
								<a class="wordGray unbind unbind-id">解绑</a>
								<?php endif; ?>
							</td>
							<td><span class="accountError tip hidden"></span></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<!--彩票end-->
</div>
<!--容器end-->
