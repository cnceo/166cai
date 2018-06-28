<!DOCTYPE html>
<html>
<head>
<meta charset="gb2312">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
<title>166彩票合作商数据管理系统</title>
<meta name="Description" content="">
<meta name="Keywords" content="">
<link rel="stylesheet" href="/caipiaoimg/dashboard/css/global.css">
<link rel="stylesheet" href="/caipiaoimg/dashboard/css/login-cp.css">
</head>
<body>
<div class="wrapper">
	<div class="loginPop">
		<div class="tit">166彩票合作商数据管理系统</div>
		<form action="" method="post" id="login">
			<ul class="loginForm clearfix">
				<li style="display: none"><span class="sErrorTip"><i
						class="iErrorIcon"></i>请输入密码</span></li>
				<li><span class="sTit">账号</span><span class="sInput"><i
						class="iUserName"></i><input type="text" class="inputTxt"
						value="" name="name" placeholder="请输入用户名"></span></li>
				<li><span class="sTit">密码</span><span class="sInput"><i
						class="iPassWord"></i><input name="pass" type="password"
						class="inputTxt"><input type="hidden" name="encrypt" value="pass|"></span></li>
				<li><a href="javascript:;" target="_self" class="aSubmitBtn submit">登&nbsp;录</a>
					<!-- 登录中 --> <a href="javascript:;" target="_self"
					class="aSubmitBtn aSubmitBtning" style="display: none">登&nbsp;录&nbsp;中</a>
					<!-- 登录中 --></li>
			</ul>
		</form>
	</div>
</div>
<script src="/caipiaoimg/dashboard/js/jquery-1.8.3.min.js"></script>
<script src="/caipiaoimg/dashboard/js/BigInt.js"></script>
<script src="/caipiaoimg/dashboard/js/RSA.js"></script>
<script src="/caipiaoimg/dashboard/js/Barrett.js"></script>
<script>
	$(".inputTxt[name=name]").bind("focus",function(){
	  if($(this).val() == "请输入用户名"){
	    $(this).val("");
	  }
	})

	$(".inputTxt[name=name]").bind("blur",function(){
	  if($(this).val() == ""){
	    $(this).val("请输入用户名");
	  }
	})

	$(".inputTxt").bind("focus",function(){
	  $(this).parent(".sInput").addClass("focus");
	  $(this).css("color","#333");
	})

	// $(".inputTxt").bind("blur",function(){
	//   $(this).parent(".sInput").removeClass("focus");
	//   $(this).css("color","#aaa");
	// })

	$(".aDelete").bind("click",function(){
	  $(this).siblings(".inputTxt").val("").focus();
	})

	$(".submit").click(function(){
		var passEle = $(".inputTxt[name=pass]");
		
		pass = rsa_encrypt( passEle.val() );
		passEle.val(pass);
		$("#login").submit();
	})

	rsa_encrypt = function( val ) {
		var rsa_n = 'B31FD13CCDA7684626351A49159B9FDD';        
		setMaxDigits(131);
		var key = new RSAKeyPair("10001", '', rsa_n);
		return encryptedString(key, val + '<PSALT>' + '<?php echo $this->pub_salt?>');
	}
</script>
</body>
</html>