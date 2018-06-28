<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>注册成功</title>
<meta content="" name="Description">
<meta content="" name="Keywords">
<meta name="renderer" content="webkit">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.css'); ?>">
<script type="text/javascript">
    var baseUrl = '<?php echo $baseUrl; ?>';
    var busiUrl = '<?php echo $busiUrl; ?>';
    var passUrl = '<?php echo $passUrl; ?>';
    var payUrl = '<?php echo $payUrl; ?>';
    var fileUrl = '<?php echo $fileUrl; ?>';
    var cmsUrl = '<?php echo $cmsUrl; ?>';
    var G = {
        busiUrl: busiUrl,
        passUrl: passUrl,
        payUrl: payUrl,
        cmsUrl: cmsUrl,
        fileUrl: fileUrl
    };
</script>
</head>
<body>

<!--top begin-->
<?php if (empty($this->uid)): ?>
    <div class="top_bar">
    	<?php $this->load->view('elements/common/header_topbar_notlogin'); ?>
    </div>
    <input type='hidden' class='uid' name='type' value='0'/>
<?php else: ?>
    <div class="top_bar">
        <?php $this->load->view('elements/common/header_topbar'); ?>
    </div>
    <input type='hidden' class='uid' name='type' value='<?php echo $this->uid; ?>'/>
<?php endif; ?>
<!--top end-->

<!--header begin-->
<div class="header">
  <div class="wrap_in">
    <div class="logo-group">
    	<h1 class="logo"><a href=""><span class="logo-txt">2345彩票网<small>A股上市公司旗下网站</small></span></a></h1>
    	<p class="slogan"><span class="slogan-txt">100%安全购彩平台</span></p>
    </div>
    <div class="aside clearfix">
      <p class="telphone"><span class="telphone-txt">电话：400-000-2345转8</span></p>
    </div>
  </div>
</div>

<!--header end-->


<div class="wrap_in p-register">
	<div class="register-resulte">
		<div class="mod-resulte resulte-success">
			<div class="mod-resulte-bd">
				<i class="icon-resulte"></i>
				<div class="resulte-txt">
					<h2 class="resulte-txt-title"><em><?php echo $this->uname;?></em>，恭喜你加入2345彩票</h2>
				</div>
			</div>
			<div class="mod-resulte-ft">
				<p>完善个人信息、购彩更便捷、账户更安全</p>
				<a href="/safe/userInfo" class="btn btn-resulte-lnk">完善信息</a>
				<p class="resulte-ft-side">想中500万吗！去<a href="/hall">购彩大厅</a>逛逛</p>
			</div>
		</div>
		<div class="qrcode-app">
		    <h3>扫码下载客户端</h3>
		    <p>大奖就在你手中！</p>
		    <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/app/qrcode.png');?>" height="153" alt="">
		    <form class="qrcode-app-form">
		    	<input type="text" class="form-item-ipt" id="tel_num" placeholder="输入手机号码获下载链接">
                <p class="dl2msg-tips"></p>
		    	<a href="javascript:;" class="btn btn-send">免费发送</a>
		    </form>
		</div>
	</div>
</div>


<!--footer beigin-->
	<!-- 为foot预留的一个空盒子 -->
	<div class="fix-foot-short-box"></div>
</div>
<div class="footer foot-short footer_login">
  <div class="wrap_in">
    <div class="copyright">Copyright © 2345网址导航 All Rights Reserved. <a href="http://www.2345.net/2345ICP.html" rel="nofollow">ICP证沪B2-20120099</a>  法务联系：ligc@2345.com<br>A股上市公司旗下网站，股票代码:002195  2345.com郑重提示：请理性购彩，热心公益。本站不向未满18周岁的青少年出售彩票</div>
  </div>
</div>
<div class="pop-mask hidden"></div>
<iframe src="about:blank" class="popIframe hidden"></iframe>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js'); ?>"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/vform.min.js');?>"></script>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/comm.min.js');?>'></script>
<?php $this->load->view('v1.1/elements/common/encrypt');?>
<!--note-footer end-->
<!-- GA统计代码 -->
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/analyticstracking.js');?>"></script>
<!-- 武林榜统计代码 -->
<span style="display:none;">
<script type="text/javascript" src="http://union2.50bang.org/js/caipiao2345"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/50bang.js');?>"></script>
<!-- 百度统计代码 -->
<script>
  // 百度统计 
  var _hmt = _hmt || [];
  (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?73920d2a63aee9065feff02106ed5b0f";
    var s = document.getElementsByTagName("script")[0]; 
    s.parentNode.insertBefore(hm, s);
  })();
</script>

<!--footer end-->
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/slideFocus.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery.easing.1.3.min.js');?>" type="text/javascript"></script>
<!--[if IE 6]>
<script src="/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->
<script>
$(function(){
    $('.btn-send').click(function(){
        var uid = $('.uid').val();
        var tel_num = $("#tel_num").val();
        if(tel_num == ''){
            $('.dl2msg-tips').html("请输入手机号码！");
            return;
        }else if(!(/^1[3-8]{1}\d{9}/.test(tel_num)) || tel_num.length != 11){
            $('.dl2msg-tips').html("请输入正确手机号码！");
            return;
        }
                    
        $.ajax({
            type: "POST",
            url: "/app_buy/sendSms",
            data: {
                'uid': uid,
                'tel_num':tel_num
                },
            dataType: "json",
            success: function (resp) {
                if (resp.ok) {
                    $('.dl2msg-tips').html("链接已发送，请注意查收！");              
                }
                else {
                    $('.dl2msg-tips').html(resp.msg);
                }
            }
        })
    })
});

</script>
</body>
</html>
