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
<div class="wrap_in l-concise l-concise-col">
	<div class="l-concise-hd">
		<div class="steps steps-2">
			<ol>
				<li class="active"><span><i>1</i>安全验证</span></li>
				<li><span><i>2</i>重置手机号</span></li>				
			</ol>
		</div>
	</div>
	<div class="l-concise-bd modifyUserPhone">
		<div class="l-concise-main">
			<!-- 第一步 -->
			<div class="form form-modifyUserPhone">
				<div class="form-item form-tel-vcode">
					<label class="form-item-label">手机号：</label>
					<div class="form-item-con">
						<span class="form-item-txt"><?php echo substr_replace($this->uinfo['phone'],'****',3,4);?></span>
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
						<input type="text" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent" name="modifyCaptcha">
						<a href="javascript:;" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer btn-disabled" target="_self">获取验证码</a>
            			<span href="javascript:;" class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
						<div class="form-tip hide">
							<i class="icon-tip"></i>
							<span class="form-tip-con tip modifyCaptcha"></span>
							<s></s>
						</div>
					</div>
				</div>
				<div class="form-item btn-group">
					<div class="form-item-con">
						<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
						<a class="btn btn-main submit" href="javascript:;">下一步</a>
					</div>
				</div>
			</div>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
</div>
<?php $this->load->view('v1.1/elements/netimer');?>
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
		if (!validate) {
			$('.captcha').html('请先滑动验证码完成校验').parent().removeClass('hide').addClass('form-tip-error');
			return;
		}
        $.ajax({
            type: 'post',
            url:  '/main/getPhcodeNE/modifyCaptcha',
            data: {'position':<?php echo $position['phone_captche']?>, 'validate':validate},
            dataType: 'json',
            success: function(response) {
            	netimer(self);
                if(!response.status) closNeTimer(1);
            },
        });
    });

    // 下一步
    new cx.vform('.modifyUserPhone', {
		renderTip: 'renderTips',
		sValidate: 'submitValidate',
        submit: function(data) {
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/safe/modifyUserPhone',
                data: data,
                dataType: 'json',
                success: function(response) {
	                if(response.status == '100'){
	                	$('.l-concise-col').html(response.data);
                    }else if(response.status == '110'){
                    	// 重新登录
                    	window.location.href='/main/login';
                    }else if (response.status == '003') {
                    	$('.modifyCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                    	$('.modifyCaptcha').show().html('请重新获取验证码');
                    	$('input[name="modifyCaptcha"]').val('');
                    }else{
                    	$('.modifyCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                    	$('.modifyCaptcha').show().html(response.msg);
                    	$('input[name="modifyCaptcha"]').val('');
                    }
                }
            });
        }
    });
});
</script>
