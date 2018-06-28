<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo $pagesUrl; ?>">
<!--[if lte IE 6]></base><![endif]-->
<link rel="stylesheet" type="text/css" href="css/migrate/reset.css" />
<link rel="stylesheet" type="text/css" href="css/migrate/dialog.css" />
<script language="javascript" type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script language="javascript" type="text/javascript" src="js/IE6_png.js"></script>
<script language="javascript" type="text/javascript" src="js/base.js"></script>
<script type="text/javascript">
$(function(){
    var baseUrl = '<?php echo $baseUrl; ?>';
	DD_belatedPNG.fix('div, ul, img, li, input,a');  //DD_belatedPNG.fix('包含透明PNG图片的标签'); 多个标签之间用英文逗号隔开。

	$("#dialogLeft").animate({ top: "0",left: "5%",opacity: "1.0"}, 2000 );
	$("#dialogRight").animate({ top: "0",opacity:"1.0"},{ queue: false, duration: 2000, complete: function(){showDialog();}});
	//对话框依次向下显示
	function showDialog(){
		var i = 2;
		$(".dia1").slideDown(800);
		setInterval(function(){
			if(i==7)return
			$(".dia"+i).slideDown(800);
			i++;
			},1500);
	}
    $('.update').click(function() {
        var data = {
            username: $('.username').val(),
            password: $('.password').val()
        };
        $.ajax({
            type: 'post',
            url: baseUrl + 'passport/login',
            data: data,
            success: function(response) {
                if (response.code == 0) {
                    location.href = baseUrl;
                }
            }
        });
    });
    $('.next-time').click(function() {
        $('.old-form').submit();
    });
});
</script>
<title>网站升级</title>
</head>

<body>
	<div id="dialogLeft">
    	<img src="images/migrate/people.png" />
    </div>
    <div id="dialogRight">
    	<div class="message">
        	<img src="images/migrate/bg02.png" />
        	<img src="images/migrate/bg01.png" />
        </div>
        <span class="clear"></span>
        <div class="dialogMessage">
            <ul class="mel dia1">
                <li class="singleLeftText">您将无法访问原有网站。</li>
            </ul>
            <ul class="mer dia2">
                <li class="pluralRightText">新网站完整保留了您的个<br />人信息和财产。</li>
            </ul>
            <ul class="mel melPosition dia3">
                <li class="pluralLeftText"><img src="images/migrate/icon03.png" />旧手机应用将无法继续<br />使用。</li>
            </ul>
            <ul class="mer dia4">
                <li class="pluralRightText">我们将在短时间内迅速超越<br />原有网站。</li>
            </ul>
            <ul class="mel dia5">
                <li class="singleLeftText">新网站支持的彩种有限。</li>
            </ul>
            <ul class="mer merPosition dia6">
            <li class="pluralImgRightText"><img src="images/migrate/icon04.png" />我们早已准备好了全新<br /> 应用。&nbsp;<a href="<?php echo $pagesUrl; ?>apk/lottery_android.apk">下载应用</a></li>
            </ul>
        </div>
        <div class="btn">
            <p class="btnL update" style="cursor: pointer;"><a></a></p>
            <p class="btnR next-time" style="cursor: pointer;"><a></a></p>
        </div>
    </div>
    <form method="POST" class="old-form" action="http://www.51caixiang.com/v1/user/doLogin" style="display: none;">
        <input name="uid1" class="username" type="hidden" value="<?php echo $username; ?>" />
        <input name="pwd1" class="password" type="hidden" value="<?php echo $password; ?>" />
    </form>
</body>
</html>
