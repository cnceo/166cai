<!doctype html> 
<html> 
<head> 
<meta charset="utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/> 
<meta>
<title>邀请好友赢彩金</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/active/join-us.min.css');?>">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js');?>"></script>
</head>
<body>
	<?php if (empty($this->uid)): ?>
        <div class="top_bar">
        	<?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
        </div>
    <?php else: ?>
        <div class="top_bar">
            <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
        </div>
    <?php endif; ?>
    </div>
	<div class="join-wrap">
		<div class="join-hd">
			<h1>邀请好友赢千元彩金</h1>
			<p>每成功邀请1位好友，获得一次抽奖机会</p>
			<p>好友还能免费领取188元红包</p>	
		</div>
		<div class="wrap join-bd">			
			<div class="join-invite">
				<!-- 第一步 start -->
				<form action="" class="form form-join">
					<strong>您的好友赠送了您188元红包</strong>
					<div class="form-item">
						<input type="text" class="vcontent" placeholder="请输入您的手机号" c-placeholder="请输入您的手机号" data-rule="lxcheck" name="lxcheck" data-ajaxcheck='1'>
						<div class="form-tip"><i class="icon-tip"></i><span class="form-tip-con tip lxcheck">请输入正确的手机号码</span><s></s></div>
					</div>
					<div class="form-item form-vcode vcode-img">
						<div class="form-item-con">
							<input class="vcontent" type="text" placeholder="请输入图片验证码" c-placeholder="请输入图片验证码" name="imgCaptcha" data-rule="checkrsgcode" data-ajaxcheck='1' value="" />
							<img id="captcha_reg" src="/mainajax/captcha?v=<?php echo time();?>" width="68" height="30" alt="" />
							<a class="lnk-txt chg_captcha" href="javascript:;">换一张</a>
							<div class="form-tip hide"><i class="icon-tip"></i><span class="form-tip-con tip imgCaptcha"></span><s></s></div>
						</div>
					</div>
					<div class="form-item form-vcode">
						<input type="text" class="vyzm vcontent" data-rule="checkcode" name="joinCapche" placeholder="输入4位验证码" c-placeholder="输入4位验证码">
						<a href="javascript:;" id="btn-getYzm" class="lnk-getvcode _timer">获取验证码</a>
						<span class="lnk-getvcode lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
						<div class="form-tip"><i class="icon-tip"></i><span class="form-tip-con tip joinCapche">输入4位验证码</span><s></s></div>
					</div>
					<a href="javascript:;" class="btn btn-specail submit">马上领取188元红包</a>
				</form>
				<!-- 第一步 end -->
			</div>

			<div class="join-rule join-rule2">
				<h2>活动规则</h2>
				<ol>
					<li><span>1</span>邀请好友必须为新用户，每个手机号只能参加一次。</li>
					<li><span>2</span>好友领取的188元红包价值如下：</li>
						<ul><li>a. 3元注册红包（实名认证后可用）</li><li>b. 2元红包（充值20元及以上可用），5个</li><li>c. 5元红包（充值50元及以上可用），5个</li><li>d. 10元红包（充值100元及以上可用），5个</li><li>e. 20元红包（充值200元及以上可用），5个</li></ul>
					</li>
					<li><span>3</span>红包有效期为30天，逾期未使用的红包将被系统收回。</li>
					<li><span>4</span>充值时手动勾选充值红包即可使用。</li>
					<li><span>5</span>活动充值与赠送的红包不能提现，只能用于购彩，中奖奖金可提现。</li>
					<li><span>6</span>活动严禁作弊，一经发现，166彩票网有权不予赠送、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。</li>
					<li><span>7</span>关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
				</ol>
			</div>
		</div>
	</div>
	<?php $this->load->view('v1.1/elements/common/footer_academy');?>
	<script type="text/javascript">
	$(function(){
		$('#btn-getYzm').click(function(){
			var self = $(this);
			$('.imgCaptcha').closest('.form-tip').removeClass('form-tip-error');
		    $('.phone').closest('.form-tip').removeClass('form-tip-error');
	        var phone = $('.form-join input[name="lxcheck"]').val();
	        if(!phone){
	        	$('.phone').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
	        	$('.phone').show().html('请输入手机号码');
	        	return false;
	        }
	        var code = $('.form-join input[name="imgCaptcha"]').val();
	        if(!code){
	        	$('.imgCaptcha').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
	        	$('.imgCaptcha').show().html('请输入图形验证码');
	        	return false;
	        }
	        var captchaErr = $('.imgCaptcha').closest('.form-tip').hasClass('form-tip-error');
	        var phoneErr = $('.phone').closest('.form-tip').hasClass('form-tip-error');
	        if(!phoneErr && !captchaErr)
	        {   
	        	timer(self);	
	            $.ajax({
	                type: 'post',
	                url:  '/main/getPhoneCode/joinCapche',
	                data: {'phone':phone, 'position':<?php echo $position['activity_captche']?>, 'code':code, phflag:1},
	                dataType: 'json',
	                success: function(response) {
	                    if(response.status){
	                    	$('.form-join input[name="imgCaptcha"]').attr('data-ajaxcheck', '0');
	                    }else{
	                    	recaptcha_reg();
	                    	if (response.msg) {
	                    		$('.form-join input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
	                    		$('.imgCaptcha').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
	                        	$('.imgCaptcha').show().html('请输入正确的图形验证码');
	                        	closeTimer(1);
	                    	}
	                    }
	                }
	            });
			}
	    });
		new cx.vform('.form-join', {
			renderTip: 'renderTips',
			sValidate: 'submitValidate',
	        submit: function(data) {
		        data.uid = '<?php echo $uid?>';
		        data.channel = '<?php echo $channel?>';
		        data.tchl = 1;
	             var self = this;
	             $.ajax({
	                 type: 'post',
	                 url:  '/activity/joinattend',
	                 data: data,
	                 dataType: 'json',
	                 success: function(response) {
		                if (response.status === 2) {
		                	recaptcha_reg();
		                	closeTimer(1);
		                	$('.form-join input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
		                	$('.joinCapche').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
		                    $('.joinCapche').show().html('请重新获取验证码');
		                    	//注册失败
		                }else if (response.status === 1) {
		                	$('.joinCapche').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
		                    $('.joinCapche').show().html('请输入正确的手机验证码');
		                }else if (!response.status) {
			                cx.Alert({content:response.msg});
			            }else {
		                	 $(".join-invite").html('<div class="pop-join-ticket" style="line-height: 64px;"><strong><b style="position: relative;top: 4px;font-weight: bold">188</b>元红包</strong><p><b>恭喜您获得红包</b>亿万大奖等你赢</p></div><img src="/caipiaoimg/v1.1/img/active/join-us/img-qrcode-invite.png" width="200" height="120" alt="扫描二维码下载APP使用红包">')
		                }
		                $('input[name="joinCapche"]').val('');
	                 }
	             });
	        }
	    });
	})
	$(".chg_captcha").on('click', function(){
        recaptcha_reg();
        $('.form-join input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
        closeTimer(1);
        return false;
    });
	</script>
</body>
</html>