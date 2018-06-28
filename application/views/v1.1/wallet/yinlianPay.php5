<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>银联云充值-166彩票网</title>
<meta content="" name="Description">
<meta content="" name="Keywords">
<meta name="renderer" content="webkit">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>"/>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/pay.min.css');?>"/>
<script type="text/javascript">
        	var version = 'v1.1';
</script>
</head>
<body>

<!--top begin-->
<?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
<!--top end-->
<!--header begin-->
<div class="header header-short">
  <div class="wrap header-inner">
    <div class="logo">
    	<div class="logo-txt">
			<span class="logo-txt-name">166彩票</span>		
    	</div>
    	<a href="/" class="logo-img">
    		<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166.png');?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166@2x.svg');?> 2x" width="280" height="70" alt="166彩票网">
    	</a>
    	<h1 class="header-title zfb-title">银联扫码支付</h1>
    </div>
  </div>
</div>
<div class="wrap pay-container p-dsf-pay p-ylsf-pay" >
    <div class="cp-box">
        <form action="">
            <div class="product-info">
                <h2 class="tit">商品信息：彩咖充值</h2>
                <p class="buy-time">购买时间：<?php echo date('Y年m月d日 H:i:s', strtotime($params['orderTime']));?></p>
                <p class="order-num">订单编号：<?php echo $params['orderId'];?></p>
                <span class="total-money">总金额：<b><?php echo number_format(ParseUnit($params['txnAmt'], 1), 2);?></b>元</span>
            </div>

            <div class="pay-method-cont">
            	<div class="m-scan">
            		<em class="m-scan-money">&yen;<?php echo number_format(ParseUnit($params['txnAmt'], 1), 2);?></em>
            		<div class="pay-dsf-qrcode">
            			<img src="/mainajax/qrCode/<?php echo urlencode(base64_encode($params['codeUrl']));?>" alt="">
            		</div>

    	            <div class="scan-txt-tips">
    	            	<div class="icon-scan"><i></i><u></u></div>
    	            	<p>打开云闪付APP<br>扫码完成支付</p>
    	            </div>
    	            <img src="/caipiaoimg/v1.1/img/img-ylsf-scan.png" width="280" height="330" class="scan-img-tips scan-img-tips-ylsf" alt="用'云闪付'扫一扫功能扫描此二维码付款">
    	            <div class="m-scan-tips">完成支付没有提示？
    	            	<div class="mod-tips-t">
    	            		在银联云里完成支付，本页面未自动跳转，点击
    	            		<a href='/mylottery/recharge' target="_blank">「账户明细」</a>
    	            		查看支付结果！
    	            		<b></b><s></s>
    	            	</div>
    	            </div>
            	</div>

            </div>
        </form>
        <div class="pay-app-list clearfix">
            <div class="our-app">
                <img src="/caipiaoimg/v1.1/img/ylsf-qrcode.jpg" alt="" width="117" height="117">
                <span>手机扫码下载云闪付客户端</span>
            </div>
            <div class="other-app">
                <strong class="s-tit">以下APP也支持银联扫码支付</strong>
                <dl>
                    <dt>各大银行手机APP</dt>
                    <dd>
                        <em>农业银行掌上银行</em><em>中国银行掌上银行</em><em>建设银行掌上银行</em><em>交通银行手机银行</em><em>储备银行手机银行</em><em>招商银行手机银行</em><em>浦发银行手机银行</em><em>民生银行手机银行</em><em>广发银行手机银行</em><em>上海银行手机银行</em><em>平安银行手机银行</em><em>兴业银行手机银行</em>
                    </dd>
                    <dt>其他APP</dt>
                    <dd><em>手机京东，美团，大众点评...</em></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<script>
var orderId = '<?php echo $params['orderId'];?>';
$(function(){
	setTimeout('checkPay()', 5000);
});
function checkPay(){
	$.ajax({
		type: 'post',
    	url:'/ajax/checkPay',
    	dataType: 'json',
    	data: {'trade_no': orderId},
		success: function(data){
			if(data.code == 0){
				window.location.href="/mylottery/rchagscess/" + orderId;
			}else if(data.code == 2){
				setTimeout('checkPay()', 3000);
			}
		}
    })
}
</script>
<?php $this->load->view('v1.1/elements/common/footer_short');?>
