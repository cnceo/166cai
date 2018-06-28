<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>166彩票</title>
<meta content="166彩票是2345集团公司旗下的彩票移动平台，提供双色球，竞彩足球，胜负彩，竞彩篮球，超级大乐透，老11选5等热门彩种投注服务，第一时间获取开奖公告信息和中奖信息，还有彩票资讯信息哦，随时随地购彩。166彩票是彩民首选的100%安全购彩平台，中奖福地，赢家首选！" name="Description" />
<meta content="彩票，双色球，大乐透，胜负彩，竞彩足球" name="Keywords" />
<!--#include file="include/pub_css.htm"-->
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/app-download.css'); ?>">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.css');?>"/>
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
            <h1 class="logo"><a href="http://caipiao.2345.com/"><span class="logo-txt">166彩票网<small>A股上市公司旗下网站</small></span></a></h1>
            <p class="slogan"><span class="slogan-txt">100%安全购彩平台</span></p>
        </div>
        <div class="aside clearfix">
          <p class="telphone"><span class="telphone-txt">电话：400-690-6760</span></p>
        </div>
    </div>
</div>
<!--header end-->

<div class="app-dl">
  <div class="wrap_in app-dl-cnt">
    <div class="app-dl-title">
      <h2>166彩票客户端</h2>
      <p>大奖就在你手中!</p>
    </div>
    
    <div class="qrcode">
      <h3>扫描二维码下载</h3>
      <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/app/qrcode.png');?>" alt="">
    </div>
    <div class="dl2pc">
      <h3>下载到电脑</h3>
      <a href="http://app.2345.cn/caipiao/2345caipiao.apk" class="btn-dl-andr">安卓版</a>
    </div>
    <div class="dl2msg">
      <h3>短信获取下载地址</h3>
      <input type="tel" id="tel_num"  placeholder="输入手机号免费获取">
      <a href="javascript:void(0);" class="btn-sendmsg">发送短信</a>
      <p class="dl2msg-tips"></p>
    </div>

    <div class="app-show">
      <div class="slide" id="appShow">
        <div class="imgCon conList">
          <div class="con"><a href="javascript:;"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/app/app-banner1.jpg');?>" width="203" height="361" alt="" /></a></div>
          <div class="con"><a href="javascript:;"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/app/app-banner2.jpg');?>" width="203" height="361" alt="" /></a></div>
          <div class="con"><a href="javascript:;"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/app/app-banner3.jpg');?>" width="203" height="361" alt="" /></a></div>
        </div>
        <div class="tab tabList"><i class="">•</i><i>•</i><i>•</i></div>
      </div>
    </div>
  </div>
  <p class="wrap_in app-introduce"><strong>166彩票</strong>是2345集团公司旗下的彩票移动平台，提供双色球，竞彩足球，胜负彩，竞彩篮球，超级大乐透，老11选5等热门彩种投注服务，第一时间获取开奖公告信息和中奖信息，还有彩票资讯信息哦，随时随地购彩。166彩票是彩民首选的100%安全购彩平台，中奖福地，赢家首选。</p>
</div>

<!-- footer begin<login&register included> -->
<?php $this->load->view('v1.1/elements/common/footer_academy');?>
<!--note-footer end-->

<!-- footer end  <login&register included> -->

<!--#include file="include/pub_js.htm"-->
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/slideFocus.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery.easing.1.3.min.js');?>" type="text/javascript"></script>
<script>
  $(function(){
    $("#appShow").slideFocusPlugin({
      autoPlayTime: 3000,
      selectClass: "selected",
      stepNum: 203,
      tabNum: true,
      funType: 'click',
      delayTime:0,
      animateStyle: ["left","easeOutQuad"],
      animateTime: 400
    });

    function hasPlaceholderSupport() {
      var input = document.createElement('input');
      return ('placeholder' in input);
    }
    var tipBoxIpt = $('.dl2msg input'),
        message = tipBoxIpt.attr('placeholder');
    if(hasPlaceholderSupport()){
      tipBoxIpt.each(function(){
        $(this).val('');
      });
    }
    else{
      tipBoxIpt.on({
        focus: function(){
          $(this).val('');
        },
        blur: function(){
          if($(this).val() == ''){
            $(this).val($(this).attr('placeholder'));
          }
        }
      });
    }
  })
  
  $('.btn-sendmsg').click(function(){
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
                $('.dl2msg-tips').html("链接已发送至您的手机，请注意查收！");              
            }
            else {
            	$('.dl2msg-tips').html(resp.msg);
            }
        }
    })
})
  $('#tel_num').focus(function(){
		  $('.dl2msg-tips').html('');
  })
</script>
</div>
</body>
</html>
