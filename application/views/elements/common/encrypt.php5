<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/BigInt.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/Barrett.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/RSA.js');?>" type="text/javascript"></script>
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