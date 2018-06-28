<!doctype html> 
<html> 
<head> 
<meta charset="utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/> 
<meta>
<title>2016百年美洲杯球队巡礼-166彩票官网</title>
<meta content='166彩票官网最新推出“2016百年美洲杯球队巡礼”专题活动，共分为A组，B组，C组，D组，美国队，哥伦比亚队，哥斯达黎加队，巴拉圭队。166彩票网安全服务！' name="Keywords">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/template.js');?>"></script>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<style>
	body {
		background: #fff;
		color: #666;
	}
	
	.euro-wrap {
		font-size: 14px;
	}

	.euro-hd {
		background: #183f7a url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/banner-bg.jpg');?>) 50% 0 no-repeat;
	}
	.euro-hd .wrap {
		height: 468px;
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/banner.jpg');?>) 50% 0 no-repeat;
		text-indent: -150%;
		overflow: hidden;
		font-size: 0;
	}

	.euro-tab {
		margin: -80px 0 16px;
	}
	.euro-tab ul {
		width: 820px;
		margin: 0 auto;
		text-align: center;
		font-size: 0;
	}
	.euro-tab li {
		display: inline-block;
		*display: inline;
		*zoom: 1;
		width: 124px;
		height: 142px;
		margin: 0 18px;
	}
	.euro-tab a {
		display: block;
		width: 124px;
		height: 142px;
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/ca-xl-tab.png');?>) no-repeat;
		cursor: pointer;
		text-indent: -150%;
		overflow: hidden;
	}
	.euro-tab-b, .euro-tab-d, .euro-tab-f {
		position: relative;
		top: -10px;
	}
	li.euro-tab-a a {
		background-position: 0 -160px;
	}
	li.euro-tab-b a {
		background-position: -140px -160px;
	}
	li.euro-tab-c a {
		background-position: -280px -160px;
	}
	li.euro-tab-d a {
		background-position: -420px -160px;
	}
	li.euro-tab-a .current {
		background-position: 0 0;
	}
	li.euro-tab-b .current {
		background-position: -140px 0;
	}
	li.euro-tab-c .current {
		background-position: -280px 0;
	}
	li.euro-tab-d .current {
		background-position: -420px 0;
	}

	.euro-wrap h2 {
		margin: 0 0 16px 62px;
		padding-left: 30px;
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/ca-h2-bg.png');?>) 0 50% no-repeat;
		font-weight: bold;
		font-size: 26px;
	}

	.qualifier {
		position: relative;
		width: 868px;
		margin: 0 auto 48px;
		padding: 12px 0;
		border: 4px solid #ccc;
		text-align: center;
	}
	.qualifier b, .qualifier s {
		position: absolute;
		left: 66px;
		top: -8px;
		width: 0;
		height: 0;
		border-width: 0 12px 12px;
		border-style: solid;
		border-color: transparent transparent #fff;
		_border-style: dashed dashed solid;
	}
	.qualifier b {
		top: -14px;
		border-bottom-color: #ccc;
	}
	
	.euro-item {
		display: none;
	}
	.euro-item .group-item {
		position: relative;
		width: 1000px;
		height: 846px;
		margin-bottom: 20px;
		background-position: 50% 0;
		background-repeat: no-repeat;
	}
	.euro-item .group-item .inner {
		width: 844px;
		height: 679px;
		margin: 0 auto;
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/item-inner-bg.png');?>) 50% 0 no-repeat;
	}
	.country-logo {
		position: absolute;
		left: 64px;
		top: 2px;
		width: 80px;
		height: 94px;
	}
	.country-star {
		position: absolute;
		right: 60px;
		top: -27px;
		width: 187px;
		height: 110px;
	}
	.group-a .group-item {
		background-image: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/item-bg-a.png');?>);
	}
	.group-a h2, .group-a .group-item h4 {
		color: #ca3636
	}
	.group-a .qualifier {
		border-color: #ca3636;
	}
	.group-a .qualifier b {
		border-bottom-color: #ca3636;
	}
	.group-b .group-item {
		background-image: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/item-bg-b.png');?>);
	}
	.group-b h2, .group-b .group-item h4 {
		color: #0baf0a
	}
	.group-b .qualifier {
		border-color: #0baf0a;
	}
	.group-b .qualifier b {
		border-bottom-color: #0baf0a;
	}
	.group-c .group-item {
		background-image: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/item-bg-c.png');?>);
	}
	.group-c h2, .group-c .group-item h4 {
		color: #ff7e00
	}
	.group-c .qualifier {
		border-color: #ff7e00;
	}
	.group-c .qualifier b {
		border-bottom-color: #ff7e00;
	}
	.group-d .group-item {
		background-image: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/item-bg-d.png');?>);
	}
	.group-d h2, .group-d .group-item h4 {
		color: #45a2e1
	}
	.group-d .qualifier {
		border-color: #45a2e1;
	}
	.group-d .qualifier b {
		border-bottom-color: #45a2e1;
	}

	.aebly .group-item-table h4 {
		font-size: 22px;
	}

	.group-item h3 {
		height: 98px;
		margin-bottom: 12px;
		padding-left: 160px;
		line-height: 98px;
		font-size: 22px;
		color: #fff;
	}
	.group-item h3 strong {
		margin-right: 14px;
		font-weight: bold;
		font-size: 30px;
		color: #fbf803;
	}
	
	.group-item-list {
		position: relative;
		margin-bottom: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 16px;
		color: #666;
	}
	.group-item-list li {
		float: left;
		width: 281px;
		height: 138px;
		padding-top: 6px;
	}
	.group-item-list li em {
		display: block;
		color: #ff9000;
	}
	.group-item-list .euro-wcj {
		position: absolute;
		left: 295px;
		top: 64px;
		width: 46px;
		height: 70px;
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/euro-wcj.png');?>) 50% 0 no-repeat;
	}
	.group-item-title {
		margin-bottom: 19px;
		text-align: center;
		font-size: 16px;
	}
	.group-item-table .group-item-title {
		height: 42px;
	}
	.group-item h4 {
		font-weight: bold;
		font-size: 24px;
	}
	.group-item-link {
		height: 137px;
		margin-bottom: 38px;
	}
	.group-item-link ul {
		margin-right: -60px;
	}
	.group-item-link li {
		float: left;
		width: 340px;
		margin: 0 60px 17px 0;
		padding-left: 35px;
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/arrow.png');?>) 12px 50% no-repeat;
	}
	.group-item-link a {
		font-weight: bold;
		font-size: 16px;
		color: #666;
	}

	.group-item-table-bd {
		padding-top: 18px;
	}
	.group-item-table-bd .clearfix {
		margin-right: -76px;
		padding-left: 30px;
	}
	.group-item-table-bd ul li {
		float: left;
		width: 360px;
		height: 37px;
		margin-bottom: 5px;
		margin-right: 76px;
		overflow: hidden;
		*zoom: 1;
		line-height: 37px;
		font-weight: bold;
		font-size: 16px;
		color: #666;
	}
	.group-item-table-bd .col1 {
		float: left;
		width: 135px;
	}
	.group-item-table-bd .col2 {
		float: left;
		width: 73px;
		text-align: center;
		font-weight: normal;
	}
	.group-item-table-bd .col3 {
		float: left;
		width: 100px;
		padding-left: 52px;
	}
	.euro-side {
		position: fixed;
		_position: absolute;
		left: 50%;
		bottom: 200px;
		width: 126px;
		height: 220px;
		margin-left: 502px; 
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/sprite-side.png');?>) -0 0 no-repeat;
	}
	.euro-side p {
		text-indent: -150%;
		overflow: hidden;
		font-size: 0;
	}
	.euro-side .btn-start {
		position: absolute;
		left: 0;
		top: 220px;
		width: 106px;
		height: 38px;
		background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/ca-xl/sprite-side.png');?>) 0 -220px no-repeat;
		text-indent: -150%;
		overflow: hidden;
		font-size: 0;
	}
	.euro-side .btn-start:hover {
		background-position: 0 -258px;
	}
	.euro-side .btn-close {
		position: absolute;
		right: 0;
		top: 20px;
		width: 22px;
		height: 22px;
		text-indent: -150%;
		overflow: hidden;
		font-size: 0;
	}
</style>
<script type="text/javascript">
var baseUrl = '<?php echo $this->config->item('base_url'); ?>';
</script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
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
	<div class="euro-wrap">
		<div class="euro-hd">
			<div class="wrap">
				<h1>2016法国欧洲杯球队巡礼</h1>
				<p>比赛时间：2016.06.11-2016.07.11</p>
			</div>		
		</div>
		<div class="wrap euro-bd">
			<div class="euro-tab">
				<ul class="clearfix">
					<li class="euro-tab-a"><a href="javascript:;" class="current">A组</a></li>
					<li class="euro-tab-b"><a href="javascript:;">B组</a></li>
					<li class="euro-tab-c"><a href="javascript:;">C组</a></li>
					<li class="euro-tab-d"><a href="javascript:;">D组</a></li>
				</ul>
			</div>
			<div class="euro-con"></div>
		</div>
		<div class="euro-side">
			<p>欧洲杯一起赢</p>
			<a href="/jczq/hh" target="_blank" class="btn-start" title="立即预约">立即预约</a>
			<a href="javascript:;" title="关闭" class="btn-close">关闭</a>
		</div>
	</div>
	<script id="euroCon" type="text/html">
		    {{each teamInfo as group i}}
		        <div class="euro-item group-{{i}}">
		        	<h2>小组介绍语</h2>
		        	<div class="qualifier">
						<img src="../../caipiaoimg/v1.1/img/active/ca-xl/img-{{i}}.png" width="844" height="271" alt="">
						<b></b>
						<s></s>
					</div>
					<ul>
						{{each teamInfo[i] as team}}
						<li class="group-item">
							<h3><strong>{{team.name}}</strong>{{team.slogan}}</h3>
							<img src="{{team.logo}}" class="country-logo" width="80" height="94" alt="">
							<img src="{{team.starImg}}" class="country-star" width="187" height="110" alt="">
							<div class="inner">
								<div class="group-item-list">
									<ul class=" clearfix">
										<li>FIFA排名<em>{{team.rank}}</em></li>
										<li>当家球星<em>{{team.starName}}</em></li>
										<li>夺冠赔率<em>{{team.odds}}</em></li>
									</ul>
								</div>
								<div class="group-item-link">
									<div class="group-item-title">
										<h4>美洲杯看点</h4>
									</div>
									<ul class="clearfix">
										{{each team.link as linkInfo i}}
										<li>
											<a href="{{linkInfo.href}}" target="_blank">{{linkInfo.title}}</a>
										</li>
										{{/each}}
									</ul>
								</div>
								<div class="group-item-table">
									<div class="group-item-title">
										<h4>{{team.name}}队最近战绩情况</h4>
										{{if team.host}}
										<p>{{team.name}}作为东道主不参加预赛，下图为预选赛战绩</p>
										{{/if}}
									</div>
									<div class="group-item-table-bd">
										<ul class="clearfix">
											{{each team.match as match i}}
											<li>
												<span class="col1">{{match.teamZ}}</span><span class="col2">{{match.score}}</span><span class="col3">{{match.teamK}}</span>
											</li>
											{{/each}}
										</ul>
									</div>
								</div>
							</div>
						</li>
						{{/each}}
					</ul>
		        </div>
		    {{/each}}
	</script>
	<script>
	
		$(function(){
			$.get('/caipiaoimg/data/caxl.js', function(data){
				$('.euro-con').html(template('euroCon', teamData));
				$('.euro-tab').find('li').each(function(){
					if($(this).find('a').hasClass('current')){
						$('.euro-con').find('.euro-item').eq($(this).index()).show();
					}
				})
			})
			
			$('.euro-tab').on('click', 'li', function(){
				$(this).find('a').addClass('current').parents('li').siblings().find('a').removeClass('current');
				$('.euro-con').find('.euro-item').eq($(this).index()).show().siblings().hide();
			})

			$('.euro-side').on('click', '.btn-close', function(){
				$(this).parents('.euro-side').hide();
			})
		});
	</script>
	<?php $this->load->view('v1.1/elements/common/footer_academy');?>
</body>
</html>