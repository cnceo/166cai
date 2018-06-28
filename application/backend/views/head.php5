<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>首页 - 彩票管理后台</title>
    <link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/admin.css?v=9">
    <script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery-1.8.3.min.js"></script>
    <script type='text/javascript' src="/caipiaoimg/v1.0/js/ui.js"></script>
</head>
<div class="header">
    <h1 class="logo"></h1>
    <div class="account"><?php echo '您好！'.$uname?><a href="/backend/Account/pass" target="rightFrame">[修改密码]</a><a href="javascript:;" id="logout">[退出]</a></div>
</div>
<script type="text/javascript">
$("#logout").click(function(){
	window.parent.location.href = '/backend/login/out';
})
</script>