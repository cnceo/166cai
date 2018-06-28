<?php if($tracker == 'emailSever'): $this->load->view('elements/common/header_notlogin'); endif;?>
<?php if($tracker == 'emailSever'): ?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<!-- body -->
<div class="wrap_in other-container">
<?php endif; ?>
<div class="lay-find">
	<div class="hd"><h2 class="tit">找回密码</h2></div>
	<div class="bd">
		<ul class="steps-bar clearfix">
			<li><i>1</i><span class="des">输入2345账号</span></li>
			<li><i>2</i><span class="des">验证身份</span></li>
			<li class="cur"><i>3</i><span class="des">设置密码</span></li>
			<li class="last"><i>4</i><span class="des">操作成功</span></li>
		</ul>
		<div class="find-form">
			<form class="form">
				<div class="form-item">
					<label class="form-item-label">新密码</label>
					<div class="form-item-con">
						<input type="password" name="pword" data-rule="password" data-encrypt='1' value="" class="form-item-ipt vcontent" style="">
						<div class="form-tip hide form-tip-ok">
							<i class="icon-tip"></i>
							<span class="form-tip-con pword tip" style="display:none;">
								<div>密码状态</div>
								<!-- <div class="pwd_streng pwd_streng_1"><i class="on"></i><i class="on"></i><i class="on"></i><i class="on"></i><em class="streng_field">极佳</em></div>
								<div>6-16字符，区分大小写。建议使用字母、数字和符号组合</div> -->
							</span>
							<s></s>
						</div>
					</div>
				</div>
				<div class="form-item">
					<label class="form-item-label">确认密码</label>
					<div class="form-item-con">
						<input type="password" name="con_pword" data-rule="same" data-encrypt='1' data-with="pword" value="" class="form-item-ipt vcontent" style="">
						<div class="form-tip hide">
							<i class="icon-tip"></i>
							<span class="form-tip-con con_pword tip"></span>
							<s></s>
						</div>
					</div>
				</div>
				<div class="form-item btn-group">
					<div class="form-item-con">
						<a class="btn confirm submit" href="javascript:;">确定</a>
					</div>
				</div>
			</form>
			<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_3'>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		//验证提交
		new cx.vform('.find-form', {
	        renderTip: 'renderTips',
	        submit: function (data) {
				before_check(data);
				$.ajax({
		            type: 'post',
		            url: '/safe/find_password',
		            data: data,
		            success: function (response) {
		                if(response == '001'){
		                	cx.Alert({content:'系统异常！'});
                        }
                        else if(response == '002'){
                        	cx.Alert({content:'2345账号或验证码不能为空！'});
                        }
                        else if(response == '003'){
                        	recaptcha_reg();
                        	cx.Alert({content:'验证码错误！'});
                        }
                        else if(response == '004'){
                        	cx.Alert({content:'2345账号不存在！'});
                        }
                        else if(response == '005'){
                        	cx.Alert({content:'登录密码不能与支付密码一致！'});
                        }
                        else if(response == '006'){
                        	cx.Alert({content:'登录密码不正确！'});
                        }
                        else{
                            $('.other-container').html(response);
                        }
		            }
		        });
	        }
	    });

	    //data check before post
		function before_check(data){
			if(data.actiontype == '_1'){
				if(data.username == ''){
					return false;
				}
				if(data.yz == ''){
					return false;
				}
				checkname(data.username);
			}
		}

	})    

</script>
<?php if($tracker == 'emailSever'): ?>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>'></script>
<!-- body -->
<?php endif; ?>
<?php if($tracker == 'emailSever'): $this->load->view('elements/common/footer_short'); endif; ?>

