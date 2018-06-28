<?php $this->load->view('elements/common/header_notlogin');?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<!-- body -->
<div class="wrap_in other-container">
	<div class="lay-find">
		<div class="hd"><h2 class="tit">找回密码</h2></div>
		<div class="bd">
			<ul class="steps-bar clearfix">
				<li class="cur"><i>1</i><span class="des">输入2345账号</span></li>
				<li><i>2</i><span class="des">验证身份</span></li>
				<li><i>3</i><span class="des">设置2345登录密码</span></li>
				<li class="last"><i>4</i><span class="des">操作成功</span></li>
			</ul>
			<div class="find-form">
				<form action="" class="form">
					<div class="form-item">
						<label class="form-item-label">2345账号</label>
						<div class="form-item-con">
							<input type="text" value="" name="username" data-rule="username" autocomplete="off" class="form-item-ipt vcontent" id="uname">
							<div class="form-tip hide form-tip-error">
								<i class="icon-tip"></i>
								<span class="form-tip-con username tip">请填写2345账号</span>
								<s></s>
							</div>
						</div>
					</div>
					<div class="form-item form-vcode">
						<label class="form-item-label">验证码</label>
						<div class="form-item-con">
							<input type="text" value="" name="captcha" class="form-item-ipt inp_s vcontent" id="captcha" style="" data-rule="checkcode" data-ajaxcheck='1'><img id="captcha_reg" alt="" src="/mainajax/captcha?v=<?php echo time();?>">
							<a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha_reg">换一张</a>
							<div class="form-tip hide form-tip-error">
								<i class="icon-tip"></i>
								<span class="form-tip-con tip captcha">请输入验证码</span>
								<s></s>
							</div>
						</div>
					</div>
					<div class="form-item btn-group">
						<div class="form-item-con">
							<a class="btn btn-confirm submit" href="javascript:;">下一步</a>
						</div>
					</div>
				</form>
				<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
			</div>
		</div>
	</div>
	<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
	<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>'></script>
	<script type="text/javascript">
		$(function(){
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

		    //刷新验证码
			$("#change_captcha_reg").on('click', function(){
		    	recaptcha_reg();
		    	return false;
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

			//check username
			function checkname(username){
				username = $.trim(username);
				if (username.length < 2){
					return false;
				}
				if (username.replace(/[^\x00-\xff]/g, "**").length > 24){
					return false;
				}
				if (/[^\u4E00-\u9FA5\w_@\.\-]/.test(username)){
					return false;
				}
			}

		})
	</script>
</div>
<!-- body -->
<?php $this->load->view('elements/common/footer_short');?>
<!--[if IE 6]>
<script src="/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->

