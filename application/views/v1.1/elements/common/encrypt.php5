<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/encrypt.min.js');?>" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	window.cx || (window.cx = {});
	cx.pub_salt = '<?php echo $this->pub_salt?>';
	cx.rsa_encrypt = function( val ) {
		var rsa_n = 'B31FD13CCDA7684626351A49159B9FDD';        
		setMaxDigits(131);
		var key = new RSAKeyPair("10001", '', rsa_n);
		return encryptedString(key, val + '<PSALT>' + cx.pub_salt);
	}
});
</script>
<!-- 网易滑块验证码 -->
<script src="//cstaticdun.126.net/load.min.js"></script>
<script>
var initne = function (options, verify, ifun, efun) {
	if (initNECaptcha !== undefined && $.isFunction(initNECaptcha)) {
		var opt = options || {};
		initNECaptcha({
	        captchaId: opt.captchaId || '5bd03d0922574cc9a4ecab373d13b88b', // <-- 这里填入在易盾官网申请的验证码id
	        element: opt.element || '.captcha_div',
	        mode: opt.mode || 'float',
	        width: opt.width || '270px',
	        onVerify: function(err, ret){
		        if ($.isFunction(verify)) verify(err, ret);
	        }
        }, function (instance) {// 初始化成功后得到验证实例instance，可以调用实例的方法
            if ($.isFunction(ifun)) ifun(instance)
        }, function (err) {// 初始化失败后触发该函数，err对象描述当前错误信息
        	if ($.isFunction(efun)) efun(err)
        });
	}
}
</script>
<style>
.yidun.yidun--light {
	margin: 0 !important;
}
</style>