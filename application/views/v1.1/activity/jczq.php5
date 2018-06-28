<!doctype html> 
<html> 
<head> 
<meta charset="utf-8"> 
<meta name="Keywords" content="竞彩足球，彩票学院，彩票新手指南，166彩票官网">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
<meta>
<title>手把手带你入门竞彩足球_彩票学院_彩票新手指南-166彩票官网</title>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css'); ?>"/>
<style>
body {
	background: #00943a;
}
.top_bar {
	position:relative;
	z-index:990;
}
.active-font {
	font: normal 14px/1.5 '微软雅黑', arial;
}
b {
	font-weight: 600;
	color: #fff000;
}
.icon-zuqiu, .jc-item .tips i, .jc-exp i, .jc-item .yellow-bg, .jc-item .yellow-bg span, .jc-odds li span, .jc-odds .arrow {
	background-image: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/sprite-icon.png')?>);
	background-repeat: no-repeat;
}
.jc-inner {
	width: 1000px;
	margin: 0 auto;
	color: #fff;
}
.jc-header {
	min-width: 1100px;
	height: 398px;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-banner-bg.jpg')?>) center 0 no-repeat;
}
.jc-header h1 {
    font-size: 0;
	height: 100%;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-banner.jpg')?>) center 0 no-repeat;
	text-indent: -150%;
	overflow: hidden;
}
.jc-f, .jc-t, .jc-fi {
	padding: 66px 0 50px;
	background: #00943a;
}
.jc-s, .jc-fo {
	padding: 66px 0 50px;
	background: #008133;
}
.jc-btn {
	display: inline-block;
	vertical-align: middle;
	padding: 10px 18px;
	background: #dd1a3a;
	font: 600 16px/1 '微软雅黑', arial;
	color: #fff;
	*display: inline;
	*zoom: 1;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	border-radius: 2px;
}
.jc-btn:hover {
	background: #c91130;
	color: #fff;
	text-decoration: none;
}

.jc-bread {
 	height: 60px;
 	margin-top: -30px;
}
.jc-bread-inner {
	width: 1006px;
	height: 60px;
	margin: 0 auto;
}
.jc-bread ol:after {
	content: '';
	display: table;
	clear: both;
}
.jc-bread ol {
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/sprite-bread.png')?>) no-repeat;
	*zoom: 1;
}
.jc-bread .active2 {
	background-position: 0 -80px;
}
.jc-bread .active3 {
	background-position: 0 -160px;
}
.jc-bread .active4 {
	background-position: 0 -240px;
}
.jc-bread .active5 {
	background-position: 0 -320px;
}
.jc-bread li {
	float: left;
}
.jc-bread a {
	float: left;
	/*text-indent: -150%;
	overflow: hidden;*/
}
.jc-bread .what1 a {
	width: 190px;
	height: 60px;
}
.jc-bread .what2 a {
	width: 200px;
	height: 60px;
}
.jc-bread .what3 a {
	width: 250px;
	height: 60px;
}
.jc-bread .what4 a {
	width: 180px;
	height: 60px;
}
.jc-bread .what5 a {
	width: 186px;
	height: 60px;
}
.jc-footer {
	min-width: 1100px;
	padding: 74px 0 90px;
	background: #008133;
	text-align: center;
}
.jc-footer p {
	font-size: 30px;
}
.jc-footer .slogan-action {
	font-size: 48px;
}
.jc-footer .jc-btn {
	font-size: 24px;
	padding-top: 7px;
}
.aside {
	 position: fixed;
	 top: 450px;
	 right: 0;
	 z-index: 100;
	 padding: 10px 8px 10px 16px;
	 background: #fff000;
	 color: #222;
	 _position:absolute;
	 _bottom:auto;
	 _top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||0)-(parseInt(this.currentStyle.marginBottom,10)||0)));
	 _margin-bottom: 140px;
	 _top: expression(eval(document.documentElement.scrollTop + 450));
	 -webkit-border-radius: 4px 0 0 4px;
	 -moz-border-radius: 4px 0 0 4px;
	 border-radius: 4px 0 0 4px;
}
.aside p {
	margin-bottom: 8px;
}
.aside em {
	font-weight: 600;
	font-size: 16px;
}
.aside .jc-btn {
	padding: 10px 26px;
}
.jc-item {
	min-width: 1100px;
}
.jc-item-title {
	margin: 0 auto 36px;
	background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/sprite-title.png')?>) no-repeat;
	text-indent: -150%;
	overflow: hidden;
}
.jc-f .jc-item-title {
	width: 600px;
	height: 100px;
	background-position: 0 0;
}
.jc-s .jc-item-title {
	width: 520px;
	height: 100px;
	background-position: 0 -120px;
}
.jc-t .jc-item-title {
	width: 440px;
	height: 100px;
	background-position: 0 -240px;
}
.jc-fo .jc-item-title {
	width: 280px;
	height: 100px;
	background-position: 0 -360px;
}
.jc-fi .jc-item-title {
	width: 480px;
	height: 100px;
	background-position: 0 -480px;
}
.jc-item .jc-img {
	margin-bottom: 36px;
	text-align: center;
}
.jc-item em {
	color: #fff000;
}
.jc-item .tips h3 {
	margin-bottom: 6px;
	color: #fff000;
	font-size: 16px;
}
.jc-item .tips li {
	padding-left: 28px;
}
.jc-item .tips i {
	display: inline-block;
	vertical-align: -4px;
	width: 20px;
	height: 20px;
	margin-right: 8px;
	background-position: -380px -340px;
	*display: inline;
	*zoom: 1;
	*vertical-align: 0;
}
.jc-odds {
	margin-bottom: 24px;
}
.jc-odds ul {
	margin-right: -50px;
	-webkit-perspective: 1200px;
    -moz-perspective: 1200px;
    -ms-perspective: 1200px;
    -o-perspective: 1200px;
    perspective: 1200px;
}
.jc-odds li {
	position: relative;
	float: left;
	width: 300px;
	margin-right: 50px;
	text-align: center;
	-webkit-transform-style: preserve-3d;
    -moz-transform-style: preserve-3d;
    -ms-transform-style: preserve-3d;
    -o-transform-style: preserve-3d;
    transform-style: preserve-3d;
}
.jc-odds li span {
	display: block;
	width: 300px;
	height: 300px;
	margin-bottom: 20px;
	text-indent: -150%;
	overflow: hidden;
}
.jc-odds .jc-odds-stpe1 span {
	background-position: 0 0;
}
.jc-odds .jc-odds-stpe2 span {
	background-position: -320px 0;
}
.jc-odds .jc-odds-stpe3 span {
	background-position: 0 -320px;
}
.jc-odds .jc-odds-animation span {
	-webkit-animation: Rotate linear 1s forwards;
}
.jc-odds .jc-odds-animation2 span {
	-webkit-animation: Rotate linear 1s forwards 1s;
}
.jc-odds .jc-odds-animation3 span {
	-webkit-animation: Rotate linear 1s forwards 2s;
}
@-webkit-keyframes Rotate {
	to { -webkit-transform: rotateY(360deg);}
}
@-moz-keyframes Rotate {
	to { -moz-transform: rotateY(360deg);}
}
@-ms-keyframes Rotate {
	to { -ms-transform: rotateY(360deg);}
}
@-o-keyframes Rotate {
	to { -o-transform: rotateY(360deg);}
}
@keyframes Rotate {
	to { transform: rotateY(360deg);}
}
.jc-odds li u {
	display: block;
	width: 150px;
	height: 30px;
	margin: 0 auto;
	line-height: 30px;
	background: #00943a;
	text-decoration: none;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}
.arrow {
	position: absolute;
	right: -45px;
	top: 120px;
	z-index: 1;
	width: 40px;
	height: 60px;
	background-position: -400px -340px;
}
.jc-item .yellow-bg {
	display: inline-block;
	vertical-align: middle;
	height: 35px;
	margin-bottom: 18px;
	padding-left: 20px;
	background-position: 0 -660px;
	font-size: 0;
	*display: inline;
	*zoom: 1;
}
.jc-item .yellow-bg span {
	display: inline-block;
	height: 35px;
	line-height: 35px;
	margin-right: -20px;
	padding-right: 20px;
	font-size: 14px;
	color: #222;
	background-position: right -660px;
	*display: inline;
	*zoom: 1;
}
.jc-t p, .jc-fo p {
	margin-bottom: 14px;
	font-size: 16px;
}
.jc-fi img {
	margin-top: 20px;
}
.jc-fi .yellow-bg {
	margin: 0 36px 0 0;
}
.jc-s .yellow-bg span {
	font-size: 18px;
}
.jc-fi .yellow-bg {
	font-weight: 600;
	font-size: 14px;
}
.jc-t .yellow-bg em, .jc-fi .yellow-bg em {
	font-size: 20px;
	color: #222;
}
.jc-exp {
	margin-bottom: 14px;
	font-size: 16px;
}
.jc-exp em {
	font-weight: 600;
	font-size: 18px;
}
.jc-exp i {
	display: inline-block;
	vertical-align: bottom;
	width: 60px;
	height: 55px;
	margin-right: 4px;
	background-position: -320px -340px;
	*display: inline;
	*zoom: 1;
	*vertical-align: -6px;
}
.fixed {
	left: 0;
	z-index: 101;
	width: 100%;
}
.icon-zuqiu {
	display: none;
	position: absolute;
	left: -155px;
	top: 0;
	z-index: 1;
	width: 200px;
	height: 60px;
	background-position: -300px -400px;
}
.jc-bread li {
	position: relative;
	*zoom: 1;
}
.active1 .icon-zuqiu {
	display: block;
}
.bg-fix {
	position: absolute;
	left: 0;
	top: 0;
	z-index: -1;
	width: 100%;
	height: 30px;
	background: #ffe600;
}
.note-footer{ width:100%; padding-top:22px; padding-bottom:38px; background:#f9f9f9; border-top:1px solid #eeeeee; text-align:center; color:#999; line-height:20px;}
.jc-main {
	min-width: 1100px;
}
.fixed-ie6 {
	width: 1100px;
	margin: 0 auto;
}
.jc-header .fixed-ie6 {
	height: 100%;
}
    .wrap_in {
    width: 1100px;
}
.top_bar, .foot-short {min-width: 1100px; margin: 0 auto;}
</style>
</head>
<body>
<!--top begin-->
<?php if (empty($this->uid)): ?>
    <div class="top_bar">
    	<?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
    </div>
<?php else: ?>
    <div class="top_bar">
        <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
    </div>
<?php endif; ?>
</div>
<!--top end-->
<div class="active-font">
	<div class="jc-header">
		<div class="fixed-ie6">
			<h1 class="jc-inner">手把手带你入门竞彩足球</h1>
		</div>
	</div>
	<div class="jc-main">
		<div class="jc-bread">
			<div class="jc-bread-box">
				<div class="jc-bread-inner">
					<ol class="active1 png_bg">
						<li class="what1"><a href="#jcF"><!-- 1、什么是过关 --></a><i class="icon-zuqiu png_bg"></i></li>
						<li class="what2"><a href="#jcS"><!-- 2、什么是赔率 --></a></li>
						<li class="what3"><a href="#jcT"><!-- 3、什么是注数于倍数 --></a></li>
						<li class="what4"><a href="#jcFo"><!-- 4、如何算奖 --></a></li>
						<li class="what5"><a href="#jcFi"><!-- 5、各玩法介绍 --></a></li>
					</ol>
				</div>
				<div class="bg-fix"></div>
			</div>
		</div>
		<div class="jc-f jc-item" id="jcF">
			<div class="jc-inner">
				<h2 class="jc-item-title png_bg">1、什么是过关,竞彩好比游戏一样，每场比赛就是一关。在N串1的过关方式中，猜对N场比赛就能过关，过关即中奖</h2>
				<p class="jc-exp"><em><i class="png_bg"></i>举个栗子：</em>我们以胜平负玩法的2串1过关方式，投注以下两场比赛：</p>
				<div class="jc-img">
					<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-f-img1.png')?>" width="810" height="159" alt="">
				</div>
				<div class="tips">
					<h3><i class="png_bg"></i>攻略提示：</h3>
				</div>
				<ol>
					<li>1、选择串关越多，需要同时猜中的比赛就越多，过关难度也越大，但中奖后奖金会更多。</li>
					<li>2、投注多场比赛时，可同时选择多种过关方式，如投注3场比赛，同时选择2串1和3串1的过关方式，可增大中奖概率。</li>
				</ol>
			</div>
		</div>
		<div class="jc-s jc-item" id="jcS">
		    <div class="fixed-ie6">
			<div class="jc-inner">
				<h2 class="jc-item-title png_bg">2、什么是赔率,赔率是国家体彩中心根据比赛情况开出的奖金值在比赛截止投注前，赔率是不断变化的。</h2>
				<h3 class="yellow-bg png_bg"><span class="png_bg">赔率值是怎么来的？</span></h3>
				<div class="jc-odds">
					<ul class="clearfix">
						<li class="jc-odds-stpe1"><span class="png_bg">收集</span><u>博彩公司情报专员</u><i class="arrow png_bg"></i></li>
						<li class="jc-odds-stpe2"><span class="png_bg">分析</span><u>博彩公司分析员</u><i class="arrow png_bg"></i></li>
						<li class="jc-odds-stpe3"><span class="png_bg">得出赔率</span><u>博彩公司专家</u></li>
					</ul>
				</div>
				<div class="tips">
					<h3><i class="png_bg"></i>攻略提示：</h3>
					<ol>
						<li>1、赔率越高，球队赢的概率就越低，反之，赔率越低，球队赢的概率就越高。
						<li>2、在正常情况下，越接近开赛时间的赔率，参考意义越大。</li>
					</ol>
				</div>

			</div>
			</div>
		</div>
		<div class="jc-t jc-item" id="jcT">
			<div class="jc-inner">
				<h2 class="jc-item-title png_bg">3、什么是注数于倍数</h2>
				<ol>
					<li>
						<h3 class="yellow-bg png_bg"><span class="png_bg"><em>1、注数：</em>按照过关方式计算出的所有投注内容的组合数</span></h3>
						<p class="jc-exp"><em><i class="png_bg"></i>举个栗子：</em>我们投注胜平负玩法中的三场比赛，过关方式同时选择“2串1”及“3串1”组合：</p>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-t-img1.png')?>" width="810" height="189" alt="">
						</div>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-t-img2.png')?>" width="810" height="150" alt="">
						</div>
						<p>计算结果如下，共计<b>4</b>注，每注<b>2</b>元，投注金额为<b>8</b>元</p>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-t-img3.png')?>" width="1012" height="55" alt="">
						</div>
						<br><br><br>
					</li>
					<li>
						<h3 class="yellow-bg png_bg"><span class="png_bg"><em>2、倍数：</em>就很简单啦，就是在您当前所选的投注方案基础上，增加等比例的投注金额</span></h3>
						<p>计算结果如下，原方案投注金额为<b>8</b>元，加<b>10</b>倍后，投注金额为<b>80</b>元。</p>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-t-img4.png')?>" width="1012" height="55" alt="">
						</div>
					</li>
				</ol>
				<div class="tips">
					<h3><i class="png_bg"></i>攻略提示：</h3>
					<ol>
						<li>1、M个N串1的投注方式比M串N的投注方式，在注数计算上可以去掉重复的组合。
						<li>2、倍投方案中奖后，奖金也会跟随投注倍数获得翻倍的回报。</li>
					</ol>
				</div>
			</div>
		</div>
		<div class="jc-fo jc-item" id="jcFo">
		<div class="fixed-ie6">
			<div class="jc-inner">
				<h2 class="jc-item-title png_bg">4、如何算奖</h2>
				<p class="jc-exp"><em><i class="png_bg"></i>举个栗子：</em>我们仍以胜平负玩法的2串1过关方式为例，我们投注了以下两场比赛：</p>
				<div class="jc-img">
					<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-fo-img1.png')?>" width="810" height="159" alt="">
				</div>
				<p>我们整理一下，以投注<b>50</b>倍为例：</p>
				<div class="jc-img">
					<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-fo-img2.png')?>" width="810" height="81" alt="">
				</div>
				<p>过关后：奖金=单价 X 赔率1 X 赔率2 X 倍数=2元 X 2.16 X 2.03 X 50=<b>438.48元</b></p>
				<p>方案盈利=438.48元-100元=<b>338.48元</b></p>
				<div class="tips">
					<h3><i class="png_bg"></i>攻略提示：</h3>
					<p>奖金计算以出票时的赔率为准。</p>
				</div>

			</div>
		</div>
		</div>
		<div class="jc-fi jc-item png_bg" id="jcFi">
			<div class="jc-inner">
				<h2 class="jc-item-title png_bg">5、各玩法介绍,胜平负 / 让球胜平负 / 总进球 / 半全场 / 比分</h2>
				<ul>
					<li>
						<p class="yellow-bg png_bg"><span class="png_bg"><em>胜平负：</em>竞猜主队全场90分钟内(含伤停补时)的比赛结果</span></p>
						<a href="/jczq/spf?source=activity" class="jc-btn" target="_blank">投注胜平负</a>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-fi-img1.png')?>" width="810" height="210" alt="">
						</div>
					</li>
					<li>
						<p class="yellow-bg png_bg"><span class="png_bg"><em>让球胜平负：</em>竞猜主队+ - 让球数的情况下全场90分钟内(含伤停补时)的比赛结果</span></p>
						<a href="/jczq/rqspf?source=activity" class="jc-btn" target="_blank">投注让球胜平负</a>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-fi-img2.png')?>" width="930" height="220" alt="">
						</div>
					</li>
					<li>
						<p class="yellow-bg png_bg"><span class="png_bg"><em>总进球：</em>竞猜对阵双方全场90分钟内(含伤停补时)的进球数之和</span></p>
						<a href="/jczq/jqs?source=activity" class="jc-btn" target="_blank">投注总进球</a>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-fi-img3.png')?>" width="720" height="200" alt="">
						</div>
					</li>
					<li>
						<p class="yellow-bg png_bg"><span class="png_bg"><em>半全场：</em>竞猜主队在上半场45分钟(含伤停补时)和全场90分钟内(含伤停补时)的胜平负结果</span></p>
						<a href="/jczq/bqc?source=activity" class="jc-btn" target="_blank">投注半全场</a>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-fi-img4.png')?>" width="710" height="159" alt="">
						</div>
					</li>
					<li>
						<p class="yellow-bg png_bg"><span class="png_bg"><em>比分：</em>比分就最简单啦，只要竞猜主队全场90分钟内(含伤停补时)的比分结果</span></p>
						<a href="/jczq/cbf?source=activity" class="jc-btn" target="_blank">投注比分</a>
						<div class="jc-img">
							<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/jczq/jc-fi-img5.png')?>" width="610" height="160" alt="">
						</div>
					</li>
				</ul>
				<div class="tips">
					<h3><i class="png_bg"></i>攻略提示：</h3>
					<ol>
						<li>1、投注为全场90分钟（含伤停补时）的比分结果，不含加时赛及点球结果。
						<li>2、让球胜平负竞猜难度更大，赔率较胜平负玩法更高。</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<div class="jc-footer">
	<div class="fixed-ie6">
		<p><b>都学会了吗？</b></p>
		<p class="slogan-action"><b>我们来投一注吧！</b><a href="/jczq?source=bottom" class="jc-btn" target="_blank">立即预约</a></p>
	</div>
	</div>
	<div class="aside">
		<p>竞彩足球这么简单，<br>快来<em>赢取奖金</em>吧！</p>
		<a href="/jczq?source=side" class="jc-btn" target="_blank">立即预约</a>
	</div>
</div>
	<!-- footer begin<login&register included> -->
<?php $this->load->view('v1.1/elements/common/footer_academy');?>
	<!-- footer end  <login&register included> -->
	<script>
		$(function(){
			var timer = null;
			$('.jc-bread').on('click', 'a', function(){
				$('.jc-bread ol').addClass('active' + ($(".jc-bread li").index($(this).parent()) + 1));
			})
			function scrollClass (){
				if($(document).scrollTop() > 380){
					$('.jc-bread-box').addClass('fixed');
				}
				else {
					$('.jc-bread-box').removeClass('fixed');
				}

				if($(document).scrollTop() < 918){
					$('.jc-bread ol').removeClass(function(){
						return $(this).attr('class');
					});
					$('.jc-bread ol').addClass('active1 png_bg');
					if(navigator.userAgent.indexOf("MSIE 6.0") > 0){
						$('.jc-bread ol').css({'background-position': '0 0'});
						$('.icon-zuqiu').css({'display': 'block'});
					}
				}
				else if($(document).scrollTop() >= 918 && $(document).scrollTop() < 1655){
					$('.jc-bread ol').removeClass(function(){
						return $(this).attr('class');
					});
					$('.jc-bread ol').addClass('active2 png_bg');
					$('.jc-odds-stpe1').addClass('jc-odds-animation');
					$('.jc-odds-stpe2').addClass('jc-odds-animation2');
					$('.jc-odds-stpe3').addClass('jc-odds-animation3');
					if(navigator.userAgent.indexOf("MSIE 6.0") > 0){
						$('.jc-bread ol').css({'background-position': '0 -80px'});
						$('.icon-zuqiu').css({'display': 'none'});
					}
				}
				else if($(document).scrollTop() >= 1655 && $(document).scrollTop() < 2880){
					$('.jc-bread ol').removeClass(function(){
						return $(this).attr('class');
					});
					$('.jc-bread ol').addClass('active3 png_bg');
					if(navigator.userAgent.indexOf("MSIE 6.0") > 0){
						$('.jc-bread ol').css({'background-position': '0 -160px'});
						$('.icon-zuqiu').css({'display': 'none'});
					}
				}
				else if($(document).scrollTop() >= 2880 && $(document).scrollTop() < 3700){
					$('.jc-bread ol').removeClass(function(){
						return $(this).attr('class');
					});
					$('.jc-bread ol').addClass('active4 png_bg');
					if(navigator.userAgent.indexOf("MSIE 6.0") > 0){
						$('.jc-bread ol').css({'background-position': '0 -240px'});
						$('.icon-zuqiu').css({'display': 'none'});
					}
				}
				else if($(document).scrollTop() >= 3700){
					$('.jc-bread ol').removeClass(function(){
						return $(this).attr('class');
					});
					$('.jc-bread ol').addClass('active5 png_bg');
					if(navigator.userAgent.indexOf("MSIE 6.0") > 0){
						$('.jc-bread ol').css({'background-position': '0 -320px'});
						$('.icon-zuqiu').css({'display': 'none'});
					}
				}
			}
			scrollClass();
			$(window).scroll(function(){
				scrollClass();
				// clearTimeout(timer);
				// timer =setTimeout(function(){
				// 	scrollClass();
				// }, 80)
			})
		})
	</script>
	<!--[if IE 6]>
	<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/DD_belatedPNG_0.0.8a-min.js');?>"></script>
	<script>DD_belatedPNG.fix('.png_bg');</script>
	<![endif]-->
</body>
</html>