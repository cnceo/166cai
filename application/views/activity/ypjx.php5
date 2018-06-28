<!doctype html> 
<html>
<head>
<meta charset="utf-8">
<meta name="discription" content="2345彩票学院频道专为网上购彩新手设计的教程，亚盘大解析通过盘口的定义内容，着重描述有关盘口的各种规则，玩法，快速学会专业的竞彩足球彩票玩法。2345彩票网100%安全购彩平台，中奖福地，赢家首选！">
<meta name="keywords" content="亚盘大解析，盘口，彩票学院，彩票新手指南">
<title>亚盘大解析_彩票学院_彩票新手指南-2345彩票</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/global.css');?>"/>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jquery-1.8.3.min.js'); ?>"></script>
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
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js'); ?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<style>
body {
	background: #d6d6d7;
}
.active-font {
	font: 400 16px/1.6 Arial, 'Microsoft Yahei', sans-serif;
	font-family: 'Microsoft Yahei'\9;
	color: #333;
}
.top_bar {
	position:relative; 
	z-index:990;
}
b {
	font-weight: 600;
}
a:active {
	star:expression(this.onFocus=this.blur());
}
.item-2 b, .item-4 b {
	color: #ffe500;
}
.ypjq-header {
	min-width: 1000px;
	height: 370px;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/banner-bg.jpg');?>) center 0 no-repeat;
}
.banner {
	position: relative;
	z-index: 10;
	width: 700px;
	height: 240px;
	margin: 0 auto;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/banner.jpg');?>) center 0 no-repeat;
	font-size: 0;
	text-indent: -150%;
}

.lead {
	min-width: 1000px;
	height: 62px;
	padding-top: 10px;
	background: #212121;
	border-bottom: 1px solid #fff;
	color: #fff;
}
.lead p {
	width: 926px;
	margin: 0 auto;
	line-height: 1.8;
	font-size: 14px;
}
.lead em {
	float: left;
	width: 30px;
	margin-top: -5px;
	margin-right: 10px;
	font-weight: 600;
	line-height: 1.2;
	font-size: 24px;
	position: relative;
	*zoom: 1;
}

.item-1, .item-2, .item-3, .item-4, .item-5 {
	min-width: 1000px;
	position: relative;
	overflow: hidden;
}
.item-1 {
	height: 699px;
}
.item-2 {
	height: 694px;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/item-2-bg.png');?>) center 0 no-repeat;
	color: #fff;
}
.item-3 {
	height: 537px;
}
.item-4 {
	height: 750px;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/item-4-bg.png');?>) center 0 no-repeat;
	color: #fff;
}
.item-5 {
	height: 1094px;
}
.inner {
	width: 1000px;
	margin: 0 auto;
}
.main h2 {
	position: relative;
	*zoom: 1;
	margin: 34px 0 20px;
	padding-left: 100px;
	font-size: 36px;
}
h2 i {
	position: absolute;
	left: 0;
	bottom: 0;
	height: 70px;
	margin-right: 24px;
	font: 68px/1 'Tahoma';
}
.item-2 h2 i, .item-4 h2 i {
	visibility: hidden;
}
.item-4 h2 {
	margin-bottom: 40px;
}
h2 i s {
	position: absolute;
	right: -14px;
	top: 30px;
	width: 85px;
	height: 49px;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/num-fix.png');?>) center 0 no-repeat;
}
.main p {
	text-indent: 2em;
}
.img-box {
	text-align: center;
}
.item-2 .img-box {
	height: 300px;
}
.item-4 .img-box {
	height: 400px;
}
.item-arrow, .item-4 h3 i, .item-2 dd i, .aside a i, .icon-top, .icon-btm {
	background-image: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/sprite-icon.png');?>);
	background-repeat: no-repeat;
}

.item-arrow {
	position: absolute;
	left: 50%;
	top: 0;
	width: 52px;
	height: 28px;
	margin-right: -26px;
}
.item-2 .item-arrow, .item-4 .item-arrow {
	background-position: -60px 0;
}
.item-3 .item-arrow, .item-5 .item-arrow {
	background-position: 0 0;
}

.item-1 .img-box {
	margin-top: 10px;
}
.item-2 dl {
	margin: 18px 0;
	padding-left: 2em;
}
.item-2 dd {
	margin-left: 30px;
	overflow: hidden;
	*zoom: 1;
}
.item-2 dd i {
	float: left;
	width: 11px;
	height: 11px;
	margin: 9px 9px 0 0;
	background-position: 0 -60px;
}
.itme-3-spe {
	margin: 20px 0 37px;
}

.item-4, .item-5 {
	line-height: 1.8;
}
.item-4 h3 {
	font-weight: 600;
	font-size: 18px;
}
.item-4 h3 i {
	float: left;
	width: 24px;
	height: 24px;
	margin: 5px 9px 0 0;
	background-position: 0 -30px;
	text-align: center;
	font: 16px/24px 'Arial';
	color: #2c7c14;
}

.item-5-spe {
	margin: 20px 0;
}
.img-box-spe {
	margin-bottom: 20px;
	padding-left: 30px;
	text-align: left;
}

.note {
	font-size: 14px;
	color: #666;
}
.dg-footer {
	min-width: 1000px;
	height: 267px;
	background: #171717;
}
.dg-footer .inner {
	width: 820px;
	padding: 80px 0 0 180px;
}
.dg-footer .lnk-go-large {
	display: inline-block;
	*display: inline;
	*zoom: 1;
	vertical-align: middle;
	width: 208px;
	height: 68px;
	margin-left: 68px;
	border: 1px solid #fff;
}
.dg-footer .lnk-go-large a {
	float: left;
	width: 208px;
	height: 68px;
	text-align: center;
	font-weight: 600;
	color: #fff;
	line-height: 66px;
	font-size: 28px;
}
.dg-footer .lnk-go-large a:hover {
	width: 204px;
	height: 64px;
	background: #fff;
	line-height: 62px;
	text-decoration: none;
	color: #262626;
	border: 2px solid #171717;
	cursor: pointer;
}
.aside {
	display: none;
	position: fixed;
	_position: absolute;
	top: 50%;
	_top: expression(eval(document.documentElement.scrollTop+(document.documentElement.clientHeight)/2));;
	left: 50%;
	z-index: 99;
	margin: -131px 0 0 520px;
}
.menu {
	position: relative;
	width: 130px;
	padding: 25px 0 3px;
	border-left: 1px solid #fff;
}
.icon-top, .icon-btm {
	position: absolute;
	width: 51px;
	height: 51px;
	left: -26px;
}
.icon-top {
	top: -51px;
	background-position: 0 -80px;
}
.icon-btm {
	bottom: -51px;
	background-position: -60px -80px;
}
.icon-top:hover {
	background-position: 0 -140px;
}
.icon-btm:hover {
	background-position: -60px -140px;
}
.menu-inner {
	position: relative;
	left: -12px;
}
.menu-inner a {
	display: block;
	margin-bottom: 22px;
	font-size: 14px;
	line-height: 24px;
	font-weight: 600;
	color: #383838;
	overflow: hidden;
	white-space: nowrap;
}
.menu-inner a:hover {
	color: #fff;
	text-decoration: none;
}
.menu-inner a i {
	float: left;
	width: 24px;
	height: 25px;
	margin-right: 12px;
	background-position: 0 -30px;
}
.menu-inner a.active {
	color: #fff;
}
.note-footer{ width:100%; padding-top:22px; padding-bottom:38px; background:#f9f9f9; border-top:1px solid #eeeeee; text-align:center; color:#999; line-height:20px;}
.ypjq-header-inner, .lead-inner {
	width: 1000px;
	margin: 0 auto;
}
.wrap_in {
    width: 1000px;	
}
</style>
</head>
<body>
    <!--top begin-->
    <?php if (empty($this->uid)): ?>
        <div class="top_bar">
        	<?php $this->load->view('elements/common/header_topbar_notlogin'); ?>
        </div>
    <?php else: ?>
        <div class="top_bar">
            <?php $this->load->view('elements/common/header_topbar'); ?>
        </div>
    <?php endif; ?>
    <!--top end-->
    <div class="active-font">
    	<div class="ypjq-header">
    		<div class="ypjq-header-inner">
			<h1 class="banner">亚盘大解析</h1>
		    </div>
    	</div>
        <div class="lead">
            <div class="lead-inner">
    		<p><em>导语</em>买足彩总避免不了看亚盘数据分析。那什么是亚盘？亚盘的全称为让球式亚洲盘。我们都知道，足球场上两支球队的实力很多时候是不一样的，投注者大都会选择实力较强一方做为投注对象，这样，博弈游戏就无法继续下去，由此，亚盘应运而生。</p>
    	   </div>
        </div>
        <ol class="main">
    		<li class="item-1">
    			<div class="inner">
    				<h2><i>01<s class="png_bg"></s></i>什么是<b>盘口</b></h2>
    				<p>亚盘投注中，我们常会听说盘口变化，而从盘口的变化中又能大致猜测到比赛的结果，因此盘口成了许多足球比赛的风向标，那么，究竟什么是盘口呢？</p>
    				<p><b>所谓盘口，也就是比赛的让球数</b>。较常见的让球盘口规则具体如下（以下盘口规则均是主队让客队球）：</p>
    				<div class="img-box">
    					<img width="561" height="448" alt="" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/item-1-img.png');?>">
    				</div>
    				<p class="note">注：当出现客队让主队球时，会在原来的盘口前加上“受让”二字，如“受让平手/半球”，“受让半球”，输赢计算方式与上述完全一致。</p>
    			</div>
    		</li>
    		<li class="item-2">
    			<div class="inner">
    				<h2><i>02</i>什么是<b>让球盘</b></h2>
    				<p><b>让球盘又叫“独赢盘”，即在指定的比赛中投注可能胜出的球队。</b></p>
    				<dl>
    					<dt>举个例子：利物浦VS切尔西，当开盘为平手盘时，投注者投注利物浦。</dt>
    					<dd><i class="png_bg"></i>如果主队赢下至少1个球则视为利物浦独赢，投注者即获得奖金；</dd>
    	                <dd><i class="png_bg"></i>如果切尔西获胜，则投注者要输掉全部本金；</dd>
    					<dd><i class="png_bg"></i>如果打成平局，球队没有输赢，则投注者没有盈亏，退还本金，也就是俗称的“走盘”。</dd>
    				</dl>
    				<p>让球盘由<b>交战球队、让球数(即盘口)</b>及<b>水位</b>这三个部分组成。例如“曼联0.86 半球 1.00富勒姆”，一个完整的独赢盘如下图所示：</p>
    				<div class="img-box"></div>
    			</div>
    			<i class="item-arrow png_bg"></i>
    		</li>
    		<li class="item-3">
    			<div class="inner">
    				<h2><i>03<s class="png_bg"></s></i>什么是<b>水位</b></h2>
    				<p>“水位”又叫“贴水”，即<b>投注比赛双方的获胜赔率</b>。以上述“曼联0.86 半球 1.00富勒姆”为例，假设投注者投注100元买曼联独赢，如曼联取胜，则投注者除本金外赢取100×0.86=86元，如果这100元投注富勒姆后，富勒姆不败，则投注者除本金外赢取100×1.00=100元。</p>
    				<p class="itme-3-spe">“水位”分<b>高水</b>、<b>中水</b>和<b>低水</b>，详细的区间划分见下图：</p>
    				<div class="img-box">
    					<img width="778" height="227" alt="" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/item-3-img.png');?>">
    				</div>
    			</div>
    			<i class="item-arrow png_bg"></i>
    		</li>
    		<li class="item-4">
    			<div class="inner">
    				<h2><i>04</i>关于<b>盘口</b>和<b>水位</b>的变化</h2>
    				<ol>
    					<li>
    						<h3><i class="png_bg">1</i>变化的三个阶段</h3>
    						<div class="img-box"></div>
    					</li>
    					<li>
    						<h3><i class="png_bg">2</i>导致变化的根本原因</h3>
    						<p>导致盘口和水位变化的最根本原因是庄家的盈利模式，庄家的利润点在于总投注额与总赔付额的差额。庄家理想的利润就是总投注额×初盘的帖水差，但一场比赛往往进入中盘的时候，会产生对阵双方的投注额不均等，这时，庄家为了规避风险，通常会对该场比赛的盘口和水位进行调整，以期达到均衡投注的目的。说到这里，我们清楚地知道，庄家每次盘口升降和水位变化的最终目的就是为了盈利。</p>
    					</li>
    				</ol>
    			</div>
    			<i class="item-arrow png_bg"></i>
    		</li>
    		<li class="item-5">
    			<div class="inner">
    				<h2><i>05<s class="png_bg"></s></i><b>盘口</b>和<b>水位</b>的综合运用</h2>
    				<p>在知道庄家每次进行盘口升降和水位变化的最终目的是为盈利后，亚盘就不再那么神秘了，比赛中的一些胜负关系也变得有迹可循。</p>
    				<p><b>举个例子：</b>2015年亚冠小组赛，首尔FC主场对阵广州恒大的比赛中，初盘为平手盘，临场变为受让平手/半球盘。</p>
    				<p style="text-indent: 0px;">我们用表格来描述变盘前后投注者的盈亏情况，投注恒大的人我们用“恒大派”表示，同理“首尔派”即首尔FC的投注者。</p>
    				<div class="img-box">
    					<img width="748" height="259" alt="" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/item-5-img.png');?>">
    				</div>
    				<p>可以看到，初始盘口和临场盘口的主要差别在于打平之后的盈亏。比赛如果打平，初始盘口上，无论投注哪支球队，最后都返还本金；而临场盘口上，恒大派要输掉自己一半的本金，首尔派则赢取一半的奖金。</p>
    				<p class="item-5-spe">我们现在假设<b>比赛结果是平局</b>，恒大派和首尔派都各投注了100元，<b>投注总额为200元</b>。</p>
    				<div class="img-box img-box-spe">
    					<img width="602" height="340" alt="" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/item-5-img2.png');?>">
    				</div>
    				<p>综上，如果比赛分出了胜负，庄家此次的变盘，并不影响投注者的盈亏；但是比赛如果打平，那么此次变盘就起到了作用，庄家可以从不盈利的状态变为盈利1元。可以预见，庄家觉得本场比赛出现平局的可能性很大，所以才会做出的这样的盘口变动，以保证自己的盈利不受影响。而这样的变盘足以让有经验的彩民对比赛结果心中有数了。</p>
    			</div>
    			<i class="item-arrow png_bg"></i>
    		</li>
        </ol>
        
        <div class="dg-footer">
        	<div class="inner">
        		<img alt="都学会了吗？来投一注试试！！" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ypjx/footer-img.png');?>">
        		<span class="lnk-go-large"><a href="http://caipiao.2345.com/jczq?source=bottom" target="_blank">立即投注</a></span>
        	</div>
        </div>
<!--         <div class="aside"> -->
<!--         	 <div class="menu"> -->
<!--     			<div class="menu-inner"> -->
<!--     				<a class="pk active" href="#pk"><i class="png_bg"></i>什么是盘口</a> -->
<!--     				<a class="rgp" href="#rgp"><i class="png_bg"></i>什么是让球盘</a> -->
<!--     				<a class="sw" href="#sw"><i class="png_bg"></i>什么是水位</a> -->
<!--     				<a class="bh" href="#bh"><i class="png_bg"></i>盘口水位变化</a> -->
<!--     				<a class="zhyy" href="#zhyy"><i class="png_bg"></i>综合运用</a> -->
<!--     			</div> -->
<!--     			<a class="icon-top png_bg" href="javascript:;"></a> -->
<!--         	 	<a class="icon-btm png_bg" href="javascript:;"></a> -->
<!--     		</div> -->
<!--         </div> -->
        <a class="gotop" href="javascript:;"></a>
    </div>
    
	<!-- footer begin<login&register included> -->
	<?php include dirname(__FILE__).'/../elements/common/footer_academy.php5'?>
	<!-- footer end  <login&register included> -->
	<script>
		$(function(){
			var scrollTop;
			var breadOl= $('.menu a');
			var aSide = $('.aside')
			var timer = null;
			$('.pk').on('click', function(){
				$('body, html').animate({
					scrollTop: 300
		        }, 500);
		        return false;
			})
			$('.rgp').on('click', function(){
				$('body, html').animate({
					scrollTop: 1000
		        }, 500);
		        return false;
			})
			$('.sw').on('click', function(){
				$('body, html').animate({
					scrollTop: 1620
		        }, 500);
		        return false;
			})
			$('.bh').on('click', function(){
				$('body, html').animate({
					scrollTop: 2240
		        }, 500);
		        return false;
			})
			$('.zhyy').on('click', function(){
				$('body, html').animate({
					scrollTop: 3124
		        }, 500);
		        return false;
			})
			function scrollClass (){
				if(scrollTop >= 300){
					aSide.show();
				}else{
					aSide.hide();
				}
				if(scrollTop >= 583 && scrollTop < 760){
					breadOl.removeClass('active');
					$('.pk').addClass('active');
				}
				else if(scrollTop >= 760 && scrollTop < 1400){
					breadOl.removeClass('active');
					$('.rgp').addClass('active');	
				}
				else if(scrollTop >= 1400 && scrollTop < 1893){
					breadOl.removeClass('active');
					$('.sw').addClass('active');
				}
				else if(scrollTop >= 1893 && scrollTop < 2596){
					breadOl.removeClass('active');
					$('.bh').addClass('active');
				}
				else if(scrollTop >= 2596){
					breadOl.removeClass('active');
					$('.zhyy').addClass('active');
				}
			}
			scrollClass();
			$(window).on('scroll', function(){
				scrollTop = $(document).scrollTop();
				clearTimeout(timer);
				scrollClass();
				// console.log(scrollTop)

				// goTop.hide();
    // 			clearTimeout(timer);
    //             timer = setTimeout(gotopShow, 400)
			})
    		
			$(".icon-top").on('click', function() {
			 	$("body,html").animate({
					scrollTop: 0
			 	}, 400);
		 	});
		 	$(".icon-btm").on('click', function() {
			 	$("body,html").animate({
					scrollTop: $(document).height()
			 	}, 400);
		 	});
		})
	</script>
	<!--[if IE 6]>
	<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js');?>"></script>
	<script>DD_belatedPNG.fix('.png_bg');</script>
	<![endif]-->

</body></html>	

