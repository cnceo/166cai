<div class="pub-pop pop-id pop-w-min">
	<div class="pop-in">
		<div class="pop-head">
			<h2>请输入图片中的四位字母:</h2>
			<span class="pop-close" title="关闭">×</span>
		</div>
		<div class="pop-body">
			<div class="form form-yzm">
				<div class="form-item">
					<label for="checkIdBank" class="form-item-label"><img id="captcha_reg" src="/mainajax/captcha?v=<?php echo time();?>" width="80" height="30" alt=""></label>
					<input type="text" class="form-item-ipt vcontent" name="imgCaptcha" data-rule="checkrsgcode" data-noblur="1">
					<a href="javascript:;" class="lnk-txt" onclick="_hmt.push(['_trackEvent', 'register', 'change_captcha']);" id="change_captcha_reg">换一张</a>
					<div class="form-tip hide">
		                <i class="icon-tip"></i>
		                <span class="form-tip-con tip imgCaptcha"></span>
		                <s></s>
		            </div>
				</div>
				<div class="form-item btn-group">
		          <a class="btn-pop-confirm submit" target="_self" href="javascript:;">提交</a>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
.form-yzm .form-tip-true {
width:40px;
opacity:0;
filter: alpha(opacity=0);
}
</style>
<script>
var code;
$("#change_captcha_reg").on('click', function(){
    recaptcha_reg();
    return false;
});
var yzmform = new cx.vform('.form-yzm', {
	renderTip: 'renderTips',
    submit: function(data) {
    	code = $("input[name=imgCaptcha]").val();
    	if ($.isFunction(yzmSub)) {
    		yzmSub();
    	}else{
    		$('.pop-w-min').remove();
        	cx.Mask.hide();
       	}
    }
});
</script>