<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>彩票后台系统-登录</title>
<link rel="stylesheet" href="../caipiaoimg/v1.0/styles/admin/login.css">
</head>
<body>
  
<div class="loginCon">
  <form action="login/dologin" id="login" method="post">
    <p class="pForm clearfix">
    <?php if ($username) {?>
    <input type="text" name="name" class="inputTxt" id="nameInput" value="<?php echo $username?>" disabled>
    <?php }else {?>
    <input type="text" name="name" class="inputTxt" id="nameInput" value="用户名：">
	<?php }?>
      <span class="sPassWord"><input type="password" value="" name="pass" class="inputTxt" id="passwordInput" ><em>密码：</em></span>
      <a class="submitBtn" href="javascript:;" class="inputTxt">GO</a>
    </p>
  </form>
</div>
<script src="/caipiaoimg/v1.0/js/jquery-1.8.3.min.js"></script>
<script src="/caipiaoimg/v1.0/js/BigInt.js"></script>
<script src="/caipiaoimg/v1.0/js/RSA.js"></script>
<script src="/caipiaoimg/v1.0/js/Barrett.js"></script>
<script>
<?php if ($wrongpass) {?>
$(function(){
	alert('用户名/密码错误！');
})
<?php }?>
var nameInputId = document.getElementById("nameInput"),passwordInputId = document.getElementById("passwordInput"),passowrdTxt = passwordInputId.parentNode.children[1];

if(passwordInputId.value != ""){
  passowrdTxt.style.display = "none";
}

nameInputId.onfocus = function(){
  if(this.value == "用户名："){
    this.value = "";
  }
}

nameInputId.onblur = function(){
  if(this.value == ""){
    this.value = "用户名：";
  }
}

passwordInputId.onfocus = function(){
  passowrdTxt.style.display = "none";
}

passwordInputId.onblur = function(){
  if(this.value == ""){
    passowrdTxt.style.display = "block";
  }
}

$(".submitBtn").click(function(){
	pass = rsa_encrypt( $("#passwordInput").val() );
	$("#passwordInput").val(pass);
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