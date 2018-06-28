<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>166彩票合作商数据管理系统</title>
<link rel="stylesheet" href="/caipiaoimg/dashboard/css/dashboard.css">
<script src="/caipiaoimg/dashboard/js/jquery-1.8.3.min.js"></script>
</head>
<body>
	<div class="header">
		<h1 class="logo"></h1>
		<div class="account">欢迎您，<?php echo $this->session->userdata('uname' );?>！<a
				href="/chansysindex/modifyPwd">[修改密码]</a><a
				href="/chansysindex/logout">[退出]</a>
		</div>
	</div>
	<div class="wrapper clearfix">

	</div>
	<div class="frame-column">
		<div class="side-nav">
			<h3>
				<a href="javascript:;"><i></i>合作商管理系统<s></s></a>
			</h3>
			<ul class="sub-nav">
				<li><a href="<?php echo $this->config->item('base_url')?>/shop">投注站管理<s></s></a></li>
			</ul>
		</div>
		<script>
		window.onload = function(){
			$(".side-nav h3:not('.home')").on("click", function () {
			    $(this).next(".sub-nav").eq(0).slideToggle();
			  });  
		}
		</script>
	</div>	
</body>
</html>