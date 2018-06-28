<?php $position = $this->config->item('POSITION')?>
<style>
.captcha_div{
	display: inline-block;
    margin-right: 10px;
    vertical-align: middle;
    *display: inline;
    *zoom: 1;
}
</style>
<div class="l-concise-hd">
	<div class="steps steps-2">
		<ol>
			<li><span><i>1</i>安全验证</span></li>
			<li class="active"><span><i>2</i>重置手机号</span></li>				
		</ol>
	</div>
</div>
<div class="l-concise-bd modifyUserPhone">
	<div class="l-concise-main">
		<!-- 第一步 -->
		<div class="form form-modifyUserPhone">
			<div class="form-item">
				<label class="form-item-label">手机号：</label>
				<div class="form-item-con">
					<input class="form-item-ipt vcontent" type="text" autocomplete="off" name="modifyPhone" data-rule="modifyPhone" data-ajaxcheck='1' value="" />
					<div class="form-tip">
						<i class="icon-tip"></i>
						<span class="form-tip-con tip modifyPhone">请输入手机号</span>
						<s></s>
					</div>
				</div>
			</div>
			<div class="form-item">
				<label class="form-item-label">滑块验证码：</label>
				<div class="form-item-con">
					<div class="captcha_div"></div>
					<div class="form-tip hide">
        				<i class="icon-tip"></i>
        				<span class="form-tip-con tip captcha"></span>
        				<s></s>
        			</div>
				</div>
			</div>
			<div class="form-item form-vcode">
				<label class="form-item-label">验证码：</label>
				<div class="form-item-con">
					<input type="text" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent" name="phoneCaptcha">
					<a href="javascript:;" id="btn-getYzm" data-freeze="modifyPhone" class="lnk-getvcode _timer btn-disabled" target="_self">获取验证码</a>
					<span href="javascript:;" class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
					<div class="form-tip hide">
						<i class="icon-tip"></i>
						<span class="form-tip-con tip phoneCaptcha"></span>
						<s></s>
					</div>
				</div>
			</div>
			<div class="form-item btn-group">
				<div class="form-item-con">
					<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_2'>
					<a class="btn btn-main submit" href="javascript:;">下一步</a>
				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view('v1.1/elements/common/appdownload');?>
</div>

<script type="text/javascript">
var validate = '', verify = function(err, ret){
    if(!err) {
        validate = ret.validate;
        $('#btn-getYzm').removeClass('btn-disabled');
        $('.captcha').parent().removeClass('form-tip-error').addClass('hide');
    }
}, initNeFun = function() {
	validate = '';
	initne({}, verify);
}
$(function() {
	initNeFun();
	// 发送验证码
	$('#btn-getYzm').click(function(){
		var self = $(this);
        var phone = $('input[name="modifyPhone"]').val();
        if(phone == '') {
        	cx.Alert({content:'请输入手机号码'});
        	return false;
        }
        if (!validate) {
			$('.captcha').html('请先滑动验证码完成校验').parent().removeClass('hide').addClass('form-tip-error');
			return;
		}
        var phoneErr = $('.modifyPhone').closest('.form-tip').hasClass('form-tip-error');
        if(!phoneErr){
            $.ajax({
                type: 'post',
                url:  '/main/getPhcodeNE/modifyYzm',
                data: {'phone':phone, 'position':<?php echo $position['phone_captche']?>, 'validate':validate},
                dataType: 'json',
                success: function(response) {
                	netimer(self);
                    if(!response.status) closNeTimer(1);
                },
            });
        }
    });

    // 下一步
    new cx.vform('.modifyUserPhone', {
		renderTip: 'renderTips',
		sValidate: 'submitValidate',
        submit: function(data) {
            var self = this;
            data.captcha = '<?php echo (string)$modifyCaptcha?>';
            $.ajax({
                type: 'post',
                url:  '/safe/modifyUserPhone',
                data: data,
                success: function(response) {
	                if(response.status == '200'){
	                	$('.l-concise-col').html(response.data);
                    }else if(response.status == '110'){
                    	// 重新登录
                    	window.location.href='/main/login';
                    }else if (response.status == '003') {
                    	$('.phoneCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                    	$('.phoneCaptcha').show().html('请重新获取验证码');
                    	$('input[name="phoneCaptcha"]').val('');
                    }else{
                    	$('.phoneCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                    	$('.phoneCaptcha').show().html(response.msg);
                    	$('input[name="phoneCaptcha"]').val('');
                    }
                }
            });
        }
    });
});
</script>
