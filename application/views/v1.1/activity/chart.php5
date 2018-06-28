<!doctype html> 
<html><head>
<meta charset="utf-8">
<title>双色球教你怎么看走势图_彩票学院_彩票新手指南-166彩票官网</title>
<meta name="Keywords" content="双色球教你怎么看走势图，双色球，走势图，彩票新手指南，彩票教程，166彩票官网">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<style>
body{ background:#f1f1f1; }
.top_bar {
	position:relative; 
	z-index:990;
}
.active-font{font:12px/1.5 microsoft yahei,Arial}
a,a:link,a:hover,a:active{ outline:none;}
.active-font a:hover{ text-decoration:none; color:#fff;}
.bannerwarp{ width:100%; height:346px; background:#fff;}
.bannercenter{ margin:0 auto; height:272px; background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/bannerbg.jpg');?>) no-repeat top center; background-size:cover; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover;}
.bannercenter img{ width:1000px; height:329px; display:block; margin:0 auto; position:relative;}
.bannercenter p{ width:1000px; height:40px; overflow:hidden; margin:0 auto; margin-top:-42px; position:relative;}
.bannercenter p em{ width:657px; margin-left:84px; text-indent:2em; display:block; font-size:14px; color:#333; line-height:20px;}
.centerWarp{ width:1000px; margin:0 auto;}
.listWarp{ width:100%; height:50px; margin:20px 0;}
#forfix{ width:100%;}
.list_four{ width:1000px; height:50px; margin:0 auto; background:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/listbg.png');?>) no-repeat; overflow:hidden; zoom:1;}
.list_four a{ width:250px; height:50px; display:inline-block; float:left; text-align:center; line-height:50px; color:#fff; font-size:24px;}
.list_four a:hover,.list_four .curr{ background:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/hoverlist.png');?>) no-repeat; _background-image:none;_filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled='true', sizingMethod='image', src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/hoverlist.png');?>");}
.list_fourFix{ position:fixed; top:0; left:0; z-index:88; _position:absolute; _top:expression(eval(document.documentElement.scrollTop));}
.module_warp{ border:1px solid #ddd; border-top:2px solid #db352d; background:#fff; margin-bottom:30px;}
.module_top{ height:72px; background:#f8f8f8; padding-left:76px; line-height:72px; color:#db352d; font-size:26px; font-weight:bold; position:relative;}
.module_top span{ width:38px; height:64px; display:block; position:absolute; left:19px; top:-2px; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:-224px -114px; font-family:Arial; font-size:24px; font-weight:bold; line-height:78px; padding-left:12px; color:#f9625a; overflow:hidden;}
.countent_warp{ padding:0 19px}
.countent_warp h2{ color:#333; font-size:16px; line-height:24px; text-indent:2em; margin-top:13px;}
.module_infor{ border-top:1px solid #eee; margin-top:13px; padding:0 30px;}
.module_even{ margin-top:19px; padding-bottom:20px;}
.module_even h3{ height:30px; padding-left:23px; color:#db352d; font-size:20px; position:relative; overflow:hidden; zoom:1;}
.module_even h3 span{ width:20px; height:20px; display:block; text-align:center; line-height:20px; color:#f9625a; font-size:14px; font-family:Arial; position:absolute; left:0; top:5px; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:-160px -135px;}
.module_even h4{ color:#333; font-size:14px; line-height:20px; text-indent:2em; margin-top:10px;}
.module_liezi{ margin-top:14px; overflow:hidden; zoom:1;}
.liezi{ width:77px; height:84px; display:inline; float:left; background:#fff9f9; margin-left:28px;}
.liezi i{ width:15px; height:17px; display:block; background:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:-183px -135px; margin:0 auto; margin-top:19px;}
.liezi span{ display:block; text-align:center; color:#333; font-size:14px; line-height:32px;}
.chartpic{ display:inline-block; float:left; margin-left:1px;}
.conclusion{ padding-left:28px; color:#666; font-size:14px; margin-top:15px;}
.conclusion em{ font-weight:bold;}
.conclusion span{ color:#333; font-weight:bold;}
.final{ border:1px dashed #ddd; background:#fffcf6; padding:10px 0; margin-bottom:30px;}
.final h5{ padding-left:32px; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:14px -181px; font-size:14px; color:#333;}
.final p{ padding-left:33px; font-size:14px; color:#666; line-height:24px;}
.p-zsi{ text-indent:2em; color:#666; font-size:14px; line-height:20px; margin-top:23px;}
.module_antho{ margin-left:28px;}
.antholiezi{ position:relative; margin-top:13px;}
.antholiezi i{ width:15px; height:17px; display:block; position:absolute; left:0; top:2px; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:-180px -114px;}
.antholiezi span{ font-size:14px; color:#333; margin-left:20px;}
.antho_left{ width:310px; height:424px; display:inline-block; float:left; background:#f8f8f8; border:1px solid #eee; margin-top:10px;}
.antho_left p{ text-align:center; color:#666; font-size:14px; margin-top:12px;}
.qushi-table{ width:133px; border:1px solid #ddd; margin:0 auto; margin-top:10px; border-collapse:collapse;}
.qushi-table tr th,.qushi-table tr td{ border:1px solid #ddd; text-align:center;}
.qushi-table tr th{ background:#fffdf3; line-height:36px; color:#666; font-size:14px; font-weight:bold;}
.qushi-table tr td{ line-height:26px; color:#999; font-family:Arial;}
.qushi-table tr .boldfont{ color:#333; font-weight:bold;}
.centerRoower{ width:40px; height:40px; display:inline-block; float:left; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:-116px -114px; margin:208px 31px 0 31px;}
.antho_right{ width:335px; height:256px; padding:0 20px; border:1px dashed #ddd; background:#fffcf6; margin-top:94px; display:inline-block; float:left; }
.antho_right h6{ font-size:14px; margin-top:15px; color:#666;}
.jilv_tab{ width:333px; border:1px solid #ddd; margin-top:7px; margin-bottom:6px; border-collapse:collapse; background:#fff;}
.jilv_tab tr th,.jilv_tab tr td{ border:1px solid #ddd; padding-left:10px; text-align:left;}
.jilv_tab tr th{ background:#f8f8f8; line-height:36px; color:#666; font-size:14px; font-weight:bold;}
.jilv_tab tr td{ line-height:26px; color:#333; font-family:Arial;}
.antho_right p{ padding-left:21px; color:#666; font-size:14px; line-height:24px; position:relative; zoom:1;}
.antho_right p em{ color:#333; font-weight:bold; font-size:14px;}
.antho_right p i{ width:15px; height:15px; display:block; position:absolute; left:0; top:4px; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:-160px -114px;}
.last-qushi{ font-size:14px; color:#666; margin-top:15px; margin-bottom:25px;}
.goplay{ width:216px; height:54px; display:block; margin:0 auto; color:#fff; font-size:26px; font-weight:bold; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; line-height:54px; margin-bottom:60px; overflow:hidden; padding-left:60px;}
.goplay:hover{ background-position:left -57px;}
.gotoTop{ width:55px; height:55px; display:block; position:fixed; left:50%; margin-left:530px; bottom:135px; margin-top:-27px; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/qushibg.png');?>); background-repeat:no-repeat; background-position:left -114px; display:none;}
.gotoTop:hover{background-position:-58px -114px;}
* html .gotoTop{position:absolute; _top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-135));}
								  
/*note-footer*/
.note-footer{ width:100%; padding-top:22px; padding-bottom:38px; background:#f9f9f9; border-top:1px solid #eeeeee; text-align:center; color:#999; line-height:20px;}
.bannercenter h1 { width: 1000px; height: 329px; margin: 0 auto; background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/banner.jpg');?>) center 0 no-repeat; font-size: 0; text-indent: -250%; overflow: hidden;}
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
	<!--banner start-->
	<div class="bannerwarp">
    	<div class="bannercenter">
        	<!-- <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/banner.jpg');?>"> -->
            <h1>双色球教你怎么看走势图</h1>
            <p><em>彩票走势图是彩票开奖号码的数据表现，用图表表示出来就是彩票走势图，研究彩票走势在投注选号的过程有着至关重要的作用。本文主要以双色球为例来讲述通过观察走势图的一些选号技巧。</em></p>
        </div>
    </div><!--bannerwarp end-->
    <!--banner end-->
    
    <!--centerWarp start-->
    <div class="listWarp">
    	<div id="forfix" class="">
        	<div class="list_four">
            	<a class="curr" href="javascript:">分区定胆</a>
                <a href="javascript:" class="">图形定胆</a>
                <a href="javascript:" class="">杀号技巧</a>
                <a href="javascript:">蓝球选号</a>
            </div>
        </div>
    </div>
	<div class="centerWarp">
        <div class="module_warp">
        	<div class="module_top"><span>01</span>分区定胆</div>
            <div class="countent_warp">
            	<h2>双色球走势图分区看法是把双色球33个红球划分为若干个特定的区域，通过统计开奖号码出现在各个区域内的次数，判断出热区和冷区，以达到精准选号的目的。常见的分区有三分区和轴四分区。</h2>
                <div class="module_infor">
                	<div class="module_even">
                    	<h3><span>1</span>三分区</h3>
                        <h4>三分区是双色球走势图分区中最常见的，它把33个红球整分为3个分区，每个区间有11个号码，分布类型以123、132、231、213、312、321、114、141、411、222这10种类型出现的最多。</h4>
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic1.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion"><em>结论：</em>2015056-058三期的分布类型是330，114，330，可知近期的热区是一区和二区，投注时可以在这两个区域内定胆。</p>
                    </div><!--module_even end-->
                    <div class="module_even">
                    	<h3><span>2</span>轴四分区</h3>
                        <h4>在双色球中轴四分区法中，一区（01-08）、二区（09-16）、三区（18-25）、四区（26-33）中，任意一个区断号都很常见。将中心号17定为盘中轴，就可以对四个分区的断区进行准确判断。相比三分区，轴四分区展示的信息更加精确。</h4>
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic2.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion"><em>结论：</em>2015056-058三期的开奖号主要集中在三区，四区发生断号的概率最高，彩民朋友可以选择在三区定胆，一二区寻找拖码。</p>
                    </div><!--module_even end-->
                </div><!--module_infor end-->
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <div class="module_warp">
        	<div class="module_top"><span>02</span>图形定胆</div>
            <div class="countent_warp">
            	<h2>双色球红球定胆最基本的方法是从历史奖号入手，对走势图上的相互关系进行分析，以判断后期奖号的范围。</h2>
                <h2>根据思路的差异，双色球定胆也可以分为概率定胆、公式算胆和图形定胆。其中图形定胆最为简单实用，准确度也高。这里主要介绍一下图形定胆的五种方法。</h2>
                <div class="module_infor">
                	<div class="module_even">
                    	<h3><span>1</span>对称走势定胆</h3>                        
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic3.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion">结论：<span>2014119期的号码13</span>和<span>2015014期的号码27</span>就是通过对称走势得到的当期的胆码。</p>
                    </div><!--module_even end-->
                    <div class="module_even">
                    	<h3><span>2</span>等距传递定胆</h3>                        
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic4.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion">结论：<span>2014115期的号码32</span>和<span>2015026期的号码13</span>就是通过等距传递得到的当期的胆码。</p>
                    </div><!--module_even end-->
                    <div class="module_even">
                    	<h3><span>3</span>对勾走势定胆</h3>                        
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic5.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion">结论：<span>2014049期的号码23</span>和<span>2015051期的号码10</span>就是通过对勾走势得到的当期的胆码。</p>
                    </div><!--module_even end-->
                    <div class="module_even">
                    	<h3><span>4</span>鱼钩走势定胆</h3>                        
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic6.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion">结论：<span>2014025期的号码10</span>和<span>2015053期的号码3</span>就是通过鱼钩走势得到的当期的胆码。</p>
                    </div><!--module_even end-->
                    <div class="module_even">
                    	<h3><span>5</span>倒三角走势定胆</h3>                        
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic7.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion">结论：<span>2014117期的号码25</span>和<span>号码10</span>就是通过倒三角走势得到的当期的胆码。</p>
                    </div><!--module_even end-->
                    <div class="final">
                    	<h5>在上述所的五种常见走势中，大家需要注意：</h5>
                        <p>1、图形具有区域性、方向性和阶段性，图形的走势会有一个从产生到消失的过程，也会被新的走势替代。</p>
                        <p>2、图形定胆必须结合奖号的具体走势进行综合判断，谨慎定胆。</p>
                        <p>3、图形定胆不是万能的，在断区的区域，号码之间没有联系，也就不能明确地判断出号码的落点了。</p>
                    </div><!--final end-->
                </div><!--module_infor end-->
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <div class="module_warp">
        	<div class="module_top"><span>03</span>杀号技巧</div>
            <div class="countent_warp">
            	<h2>双色球因其所选号码较多，中奖难度大，如果能减少选号数量，中奖机率必定增加。这就需要我们从33个号码里排除一些不太可能出现的号码，也就是彩民经常说的杀号。</h2>
                <div class="module_infor">
                	<div class="module_even">
                    	<h3><span>1</span>冷热码杀号</h3>
                        <h4>在彩民圈子里，冷热码是一种很抽象的概念，缺少一个公认的定义和界定标准。一般认为欲出几率超过2的号码为冷号，而冷号出现的几率是极低的。</h4>
                        <div class="module_liezi module_antho">
                        	<p class="antholiezi"><i></i><span>举个例子</span></p>
                            <div class="clearfix">
                            	<div class="antho_left">
                                	<p>假设近12期的号码1、2、3走势如下图所示：</p>
                                    <table class="qushi-table" cellpadding="0" cellspacing="0">
                                    	<tbody><tr>
                                        	<th width="66">期号</th>
                                            <th width="66" colspan="3">奖号</th>
                                        </tr>
                                        <tr>
                                        	<td width="66">1</td>
                                            <td width="22" class="boldfont">1</td>
                                            <td width="22" class="boldfont">2</td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">2</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">3</td>
                                            <td width="22"></td>
                                            <td width="22" class="boldfont">2</td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">4</td>
                                            <td width="22" class="boldfont">1</td>
                                            <td width="22"></td>
                                            <td width="22" class="boldfont">3</td>
                                        </tr>
                                        <tr>
                                        	<td width="66">5</td>
                                            <td width="22"></td>
                                            <td width="22" class="boldfont">2</td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">6</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">7</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">8</td>
                                            <td width="22" class="boldfont">1</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">9</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">10</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                        </tr>
                                        <tr>
                                        	<td width="66">11</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                            <td width="22" class="boldfont">3</td>
                                        </tr>
                                        <tr>
                                        	<td width="66">12</td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                            <td width="22"></td>
                                        </tr>
                                    </tbody></table>
                                </div>
                                <span class="centerRoower"></span>
                                <div class="antho_right">
                                	<h6>号码1、2、3的欲出几率计算方式见下表：</h6>
                                    <table class="jilv_tab" cellpadding="0" cellspacing="0">
                                    	<tbody><tr>
                                        	<th width="56">奖号</th>
                                            <th width="66">本期遗漏</th>
                                            <th width="66">平均遗漏</th>
                                            <th width="66">欲出机率</th>
                                        </tr>
                                        <tr>
                                        	<td>1</td>
                                            <td>4</td>
                                            <td>3</td>
                                            <td>4÷3=1.33</td>
                                        </tr>
                                        <tr>
                                        	<td>2</td>
                                            <td>7</td>
                                            <td>3</td>
                                            <td>7÷3=2.33</td>
                                        </tr>
                                        <tr>
                                        	<td>3</td>
                                            <td>1</td>
                                            <td>3.33</td>
                                            <td>1÷3.33=0.3</td>
                                        </tr>
                                    </tbody></table>
                                    <p><i></i><em>本期遗漏</em>：指该号码自上次开出之后的遗漏次数</p>
                                    <p><em>平均遗漏</em>：该号码多期遗漏的平均值</p>
                                    <p><em>欲出几率</em>=本期遗漏÷平均遗漏</p>
                                </div><!--antho_right end-->
                            </div>
                        </div><!--module_liezi end-->
                        <p class="conclusion"><em>结论：</em>因为号码2的欲出几率为2.33，大于2，所以号码2是近12期的冷号，也是我们要杀的号码。</p>
                    </div><!--module_even end-->
                    <div class="module_even">
                    	<h3><span>2</span>连号杀号</h3>
                        <h4>直连号：在某个号位上某个单码与上期重复的单码,叫做直连号；</h4>
                        <h4>斜连号：在某个号位上某个单码与上期相邻的号码,叫做斜连号。</h4>
                        <p class="p-zsi">通过对走势图的观察，不难发现，无论是直连还是斜连，出现频率较多的一般是二连号或三连号，超过三连的号码就非常稀少了，因此，当我们遇到三连号时，不妨杀掉可能出现连号的第四个号码。</p>
                        <div class="module_liezi">
                        	<div class="liezi"><i></i><span>举个例子</span></div>
                            <img class="chartpic" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/chart/charepic8.png');?>">
                        </div><!--module_liezi end-->
                        <p class="conclusion"><em>结论：</em>2015056-2015058三期的号码中，8、9、10即斜连号，20、20、20即直连号，而且均是三连，这个时候我们就可以大胆地杀掉号码<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;11和20。</p>
                    </div><!--module_even end-->
                </div><!--module_infor end-->
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <div class="module_warp">
        	<div class="module_top"><span>04</span>蓝球选号</div>
            <div class="countent_warp">
            	<h2>双色球走势图分区看法是把双色球33个红球划分为若干个特定的区域，通过统计开奖号码出现在各个区域内的次数，判断出热区和冷区，以达到精准选号的目的。常见的分区有三分区和轴四分区。</h2>
                <div class="module_infor">
                	<p class="last-qushi">投注蓝球最常见的方法是区域选号法，与红球分区选号大致相同，都是通过鉴别热冷号来选择号码，这里不再赘述。</p>                    
                </div><!--module_infor end-->
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <a class="goplay" href="<?php echo $this->config->item('pages_url')?>ssq?source=bottom" target="_blank">投一注试试</a>
        
    </div><!--centerWarp end-->
    
<a id="gotoTop" class="gotoTop" href="javascript:" style="display: none;"></a>
</div>
	<!-- footer begin<login&register included> -->
    <?php $this->load->view('v1.1/elements/common/footer_academy');?>
<!--	--><?php //include dirname(__FILE__).'/../elements/common/footer_academy.php5'?>
	<!-- footer end  <login&register included> -->
<script>
$(function()
{
	//返回顶部
	var goTop = $("#gotoTop")
	var gotopShow = function()
	{
		var scrollTop = $(window).scrollTop();
		if(scrollTop > 200)
		{
			goTop.fadeIn(400);
		}else
		{
			goTop.fadeOut(400);
		};
	};

	$(window).on('scroll', function()
	{
		gotopShow();
	});
	goTop.on('click', function() 
	{
		$("body,html").animate({scrollTop: 0}, 700);
	});
	
	//当中菜单选择
	var listA = $(".list_four a");
	var listTop;
	var flag = true;//控制最后一个curr
	
	listA.bind("click",function()
	{
		if($(this).index() == 3)
		{
			flag = false;
		}
		listTop = $(".module_warp").eq($(this).index()).offset().top - 70;
		$("body,html").scrollTop(listTop)
		$(this).addClass("curr").siblings().removeClass("curr");
		setTimeout(function(){
			flag = true;
			},500);
	});
	
	var scrollObj = $("#forfix");

	var setting  = 
	{
		scrollTop:scrollObj.offset().top
	};

	
	var title_top_info = $(".module_warp");
	$(window).bind({scroll:function()
	{
		if($(document).scrollTop() >= setting.scrollTop)
		{
			scrollObj.addClass("list_fourFix");
		}else
		{
			scrollObj.removeClass("list_fourFix");
		};
		
		if(flag)
		{
			for (var i = title_top_info.length - 1; i > -1;  i--) 
			{
				if($(window).scrollTop() >= title_top_info.eq(i).offset().top - 70)
				{
					listA.eq(i).addClass("curr").siblings().removeClass("curr");
					break;
				};
			};	
		};
	}});
});//jq
</script>
<!--[if IE 6]>
	<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/DD_belatedPNG_0.0.8a-min.js');?>"></script>
	<script>DD_belatedPNG.fix('.png_bg');</script>
	<![endif]-->
</body></html>