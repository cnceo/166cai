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
	<div class="steps steps-3">
		<ol><li><span><i>1</i>验证账号</span></li><li class="active"><span><i>2</i>安全验证</span></li><li><span><i>3</i>重置密码</span></li></ol>
	</div>
</div>
<!-- 注册 start -->
<div class="l-concise-bd">
	<div class="l-concise-main">
	   <!-- 第二步 -->
    	<div class="form findPword">
    		<div class="form-item form-tel-vcode">
    			<label class="form-item-label">手机号：</label>
    			<div class="form-item-con">
    				<span class="form-item-txt"><?php echo substr_replace($phone,'****',3,4);?></span>
    			</div>
            </div>
			<div class="form-item">
				<label class="form-item-label">验证码：</label>
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
					<a href="javascript:;" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer btn-disabled" target="_self">获取验证码</a>
            		<span class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
					<div class="form-tip hide"><i class="icon-tip"></i><span class="form-tip-con tip phoneCaptcha"></span><s></s></div>
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
<?php $this->load->view('v1.1/elements/netimer');?>
<!-- 注册 end -->
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
	$('#btn-getYzm').click(function(){
		var self = $(this); 
		if (!validate) {
			$('.captcha').html('请先滑动验证码完成校验').parent().removeClass('hide').addClass('form-tip-error');
			return;
		}
        $.ajax({
            type: 'post',
            url:  '/main/getPhcodeNE/findPwdCaptcha',
            data: {'position':<?php echo $position['login_captche']?>, 'validate':validate},
            dataType: 'json',
            success: function(response) {
            	netimer(self);
            	if(!response.status){
                	initNeFun();
                	closNeTimer(1);
                }
            }
        });
    });
	new cx.vform('.findPword', {
		renderTip: 'renderTips',
		sValidate: 'submitValidate',
        submit: function(data) {
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/safe/findPword',
                data: data,
                success: function(response) {
	                if(response.status == '000'){
	                	$('.l-concise-col').html(response.data);
                    } else if (response.status == '004') {
                		$('.phoneCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                    	$('.phoneCaptcha').show().html('请重新获取验证码');
                    } else if (response.status == '002') {
                    	$('.phoneCaptcha').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
                    	$('.phoneCaptcha').show().html('请输入正确的手机验证码');
                    } else{
                    	cx.Alert({content:response.msg});
                    }
                }
            });
        }
    });
});
</script>