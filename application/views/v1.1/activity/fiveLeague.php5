<!DOCTYPE HTML>
<!--[if lt IE 7]><html class="ie6"><![endif]-->
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if gte IE 9]><html class="ie9"><![endif]-->
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
		<title>2016竞彩足球五大联赛-166彩票官网</title>
		<meta content="166彩票官网最新推出“2016竞彩足球五大联赛”专题活动，了解英超积分榜和英超赛季前瞻。166彩票网安全服务！" name="Keywords">
		<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/template.js');?>"></script>
		<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
		<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
		<style>
		    @charset "utf-8";
		/* 五大联赛资讯页
		* @Created   : 2016-08-11
		* @Update    : 2016-02-11
		* @Author    : wuzhong
		**/
		body { font-size: 14px; font-family: Arial,\5FAE\8F6F\96C5\9ED1,\5b8b\4f53;}
		a{color: #333;}
		a:hover{color: #f37c26; text-decoration: none;}
		
		.width1000{ width: 1000px; margin: 0 auto;}
		
		.banner{ background: url(/caipiaoimg/v1.1/img/active/fiveLeagueInformation/banner.jpg) center 0 no-repeat; height: 270px; min-width: 1000px;}
		.banner .width1000{ height: 270px;}
		
		.menu{ min-width: 1000px; height: 64px; overflow: hidden; background:rgba(0,0,0,0.3); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#4C000000,endColorstr=#4C000000); margin-top: -64px;}
		:root .menu { filter:none;}
		.menu .icon{ background: url(/caipiaoimg/v1.1/img/active/fiveLeagueInformation/icon.png) 0 0 no-repeat; width: 40px; height: 40px; overflow: hidden; float: left; margin: 10px 10px 0 55px; display: inline;}
		.menu .iconA{ background-position: 0 0}
		.menu .iconB{ background-position: -40px 0}
		.menu .iconC{ background-position: -80px 0}
		.menu .iconD{ background-position: -120px 0}
		.menu .iconE{ background-position: -160px 0}
		.menu a { width: 20%; height: 60px; padding-bottom: 4px; float: left; line-height: 60px; font-size: 16px; color: #fff;}
		.menu a:hover,.menu a.cur { text-decoration: none; color: #f37f2c}
		.menu a.cur { border-bottom: 4px solid #f37f2c; padding-bottom: 0;}
		.menu a:hover .iconA,.menu a.cur .iconA{ background-position: 0 -40px}
		.menu a:hover .iconB,.menu a.cur .iconB{ background-position: -40px -40px}
		.menu a:hover .iconC,.menu a.cur .iconC{ background-position: -80px -40px}
		.menu a:hover .iconD,.menu a.cur .iconD{ background-position: -120px -40px}
		.menu a:hover .iconE,.menu a.cur .iconE{ background-position: -160px -40px}
		
		.mod_a .th_a { line-height: 54px; height: 54px; overflow: hidden;}
		.mod_a .th_a .sMark { float: left; font-size: 22px; color: #333;}
		.mod_a .th_a .aMore { float: right; font-size: 14px; color: #666;}
		.mod_a .th_a .aMore:hover { color: #f37c26}
		.mod_a .th_a .icon_txt {background: url(/caipiaoimg/v1.1/img/active/fiveLeagueInformation/map.png) -478px 0 no-repeat; width: 22px; height: 22px; float: left; margin: 16px 5px 0 0; display: inline;}
		
		.main{ padding-top: 30px;}
		.main .red { color: #e14949;}
		.main .red:hover { color: #e14949;}
		.leftCon{ float: left; width: 270px;}
		.middleCon{ float: left; width: 410px; margin-left: 40px; display: inline;}
		.rightCon{ float: right; width: 240px;}
		
		.ulRank li{ width: 100%; float: left; line-height: 36px; height: 36px; border-top: 1px dashed #eee; overflow: hidden; background: #fafafa;}
		.ulRank li.li_th{ line-height: 30px; height: 30px; border-top: 0 none; font-weight: 700; color: #fff; background: #424242; margin-bottom: -1px; position: relative;}
		.ulRank li span { float: left; text-align: center;}
		.ulRank li .sNum { width: 50px;}
		.ulRank li .sName { width: 80px; text-align: left;}
		.ulRank li .sData { width: 80px;}
		.ulRank li .sScore { width: 60px; font-weight: 700; color: #f37c26;font-family: arial}
		.ulRank li .iNum { background: url(/caipiaoimg/v1.1/img/active/fiveLeagueInformation/map.png) -463px -44px no-repeat; width: 18px; height: 18px; line-height: 18px; text-align: center; font-size: 12px; color: #fff; display: block; margin:9px auto 0;}
		.ulRank li .iRed { background-position: -482px -44px;}
		.ulRank li.li_th .sName { text-indent: 10px;}
		.ulRank li.li_th .sScore { color: #fff;}
		.ulRank li .sName .red,.ulRank li .sName .red:hover { color: #e14949}
		.ulRank li .sName .blue,.ulRank li .sName .blue:hover { color: #3969d4}
		.ulRank li.li_demote .iNum { background-position: -444px -44px;}
		.ulRank li.li_demote .sName { color: #000;}
		
		
		.pRankTips{ background: #eeeeee; padding: 5px 0 5px 18px; font-size: 12px; color: #666; line-height: 20px;}
		.pRankTips em { float: left; color: #f37c26}
		.pRankTips span { width: 186px; float: left;}
		
		.pIntro{ background: url(/caipiaoimg/v1.1/img/active/fiveLeagueInformation/map.png) 0 0 no-repeat; height: 94px; overflow: hidden; line-height: 24px; padding: 5px 5px 5px 10px; color: #fff;}
		
		.pSummary { line-height: 24px; color: #666; margin-top: 10px;}
		.pSummary em { float: left; background: url(/caipiaoimg/v1.1/img/active/fiveLeagueInformation/map.png) -456px -23px no-repeat; width: 36px; height: 20px; padding-left: 8px; font-size: 12px; font-weight: 700; color: #fff; margin: 2px 0 0 0; display: inline; line-height: 20px;}
		
		.ulTxt { padding: 10px 0 5px;}
		.ulTxt li { height: 34px; line-height: 34px; overflow: hidden; width: 100%; float: left;}
		.ulTxt li .sStyle{ float: left; color: #999; padding-right: 5px;}
		.ulTxt li a{ font-size: 16px;}
		
		.dataTable { width: 100%; font-size: 12px;}
		.dataTable th { background: #424242; line-height: 30px; height: 30px; text-align: center; border-color:#424242; color: #fff;}
		.dataTable td{ line-height: 34px; border: 1px solid #eeeeee; text-align: center; color: #333;}
		.dataTable td em { color: #666; font-weight: 700;}
		.dataTable .w115 { width: 115px;}
		.dataTable .w102 { width: 102px;}
		
		.mt15{ margin-top: 15px;}
		.mt20{ margin-top: 20px;}
		
		.adPic { width: 100%; height: 80px; overflow: hidden; padding: 13px 0 0 0;}
		.adPic img{display: block; vertical-align: top;}
		
		.tab-plugin .tab-plugin-con{ display: none;}
		.tab-plugin-extend .tab-plugin-extend-con{ display: none;}
		
		.matchTab { position: relative; height: 34px; overflow: hidden; background: #fafafa;}
		.matchTab a { float: left; width: 78px; text-align: center; padding: 4px 1px 0 1px; height: 30px; line-height: 26px; position: relative; z-index: 1; color: #666;}
		.matchTab a:hover { color: #666;}
		.matchTab a.cur {  border: 1px solid #eee; border-bottom: 0 none; border-top: 4px solid #f37c26; padding: 0; z-index: 10; background: #fff;}
		.matchTab .iLine{ height: 1px; width: 100%; position: absolute; top: 33px; left: 0; background: #eee; z-index: 5}
		
		.matchData li { width: 100%; float: left; height: 36px; line-height: 36px; overflow: hidden; border-top: 1px dashed #eee;}
		.matchData li .sTime { float: left; width: 84px; text-align: left; color: #666;}
		.matchData li .sName { text-align: center; width: 155px; float: right;}
		.matchData li .sName em { float: left;}
		.matchData li .sName .emNameLeft { width: 60px; text-align: right;}
		.matchData li .sName .emNameRight { width: 60px; text-align: left;}
		.matchData li .sName .emVs,.matchData li .sName .emScore { color: #999; font-size: 12px; width: 30px; text-align: center;}
		.matchData li .sName .emScore { color: #f37c26; }
		.matchData li.li_th { color: #999; padding-top: 10px; line-height: 28px; height: 28px; border-top: 0 none;}
		.matchData li.li_th span { color: #999;}
		
		.pTeamPic { padding: 1px 0 0 1px; overflow: hidden;}
		.pTeamPic a { float: left; border: 1px solid #dddddd; text-align: center; width: 58px; margin: -1px 0 0 -1px; display: inline; position: relative; z-index: 1;}
		.pTeamPic a img { display: block; width: 36px; height: 36px; vertical-align: top; padding-top: 5px; margin: 0 auto;}
		.pTeamPic a .sName { line-height: 18px; height: 18px; display: block; font-size: 12px; overflow: hidden;}
		.pTeamPic a:hover{ border-color: #f37c26; z-index: 5;}
		
		.teamList{ margin-top: 13px;}
		
		.rightScroll { position: fixed; top: 50%; left: 50%; margin-left: 510px; margin-top: -127px; _position: absolute;}
		.rightScroll a { background: url(/caipiaoimg/v1.1/img/active/fiveLeagueInformation/rightScrollBg.png) 0 0 no-repeat;}
		.rightScroll a.aCon { display: block; width: 95px; height: 217px; overflow: hidden; background-position: 0 0;}
		.rightScroll a.aBtn { background-position: 0 -217px; width: 95px; height: 36px; overflow: hidden; display: block;}
		.rightScroll a.aBtn:hover { background-position: 0 -254px;}
		.rightScroll a.aClose { background-position: 0 -295px; width: 21px; height: 21px; overflow: hidden; position: absolute; top: 0; left: 100px;}
		</style>
	</head>
	<body>
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
		<div class="wrapper tab-plugin-extend"> 
		  <div class="rightScroll"><a href="/jczq/hh" target="_blank" class="aCon"></a><a href="/jczq/hh" target="_blank" class="aBtn"></a><a href="javascript:;" class="aClose"></a></div>
		  <div class="banner"><div class="width1000"></div></div>
		  <div class="menu">
		    <div class="width1000">
		      <p>
		        <a href="javascript:void(0);" target="_self" class="tab-plugin-extend-tab cur"><i class="icon iconA"></i><em>英超</em></a>
		        <a href="javascript:void(0);" target="_self" class="tab-plugin-extend-tab"><i class="icon iconB"></i><em>西甲</em></a>
		        <a href="javascript:void(0);" target="_self" class="tab-plugin-extend-tab"><i class="icon iconC"></i><em>德甲</em></a>
		        <a href="javascript:void(0);" target="_self" class="tab-plugin-extend-tab"><i class="icon iconD"></i><em>意甲</em></a>
		        <a href="javascript:void(0);" target="_self" class="tab-plugin-extend-tab"><i class="icon iconE"></i><em>法甲</em></a>
		      </p>
		    </div>
		  </div>
		  <div class="main">
		    <div class="width1000 tab-plugin-extend-con" style="display:block">
		      <!-- 左侧 -->
		      <div class="leftCon">
		        <div class="mod_a">
		          <div class="th_a"><span class="sMark">英超积分榜</span></div>
		          <div class="tb_a">
		            <!-- 排行榜 -->
		            <ul class="ulRank clearfix">
		              <li class="li_th"> <span class="sNum">排名</span><span class="sName">队伍</span><span class="sData">胜/平/负</span><span class="sScore">积分</span></li>
		              <?php foreach ($score[0] as $k => $scr) {?>
		              <li <?php if ($k >= count($score[0]) - 3) {?>class="li_demote"<?php }?>>
		                <span class="sNum"><i class="iNum <?php if ($k < 6){?>iRed<?php }?>"><?php echo $k+1?></i></span>
		                <span class="sName"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $scr['tid']?>" target="_blank" class="<?php if ($k < 4) {?>red<?php }else if (in_array($k, array(4, 5))){?>blue<?php }?>"><?php echo $scr['name']?></a></span>
		                <span class="sData"><?php echo $scr['w']?>/<?php echo $scr['d']?>/<?php echo $scr['l']?></span>
		                <span class="sScore"><?php echo $scr['score']?></span>
		              </li>
		              <?php }?>
		            </ul>
		            <p class="pRankTips clearfix"><em>注：</em><span>红名可参加欧冠，蓝名可参加欧联后三名在降级区</span></p>
		            <!-- 排行榜 -->
		          </div>
		        </div>
		      </div>
		      <!-- 左侧 -->
		
		      <!-- 中间 -->
		      <div class="middleCon">
		        <!-- 赛季前瞻 -->
		        <div class="mod_a">
		          <div class="th_a"><i class="icon_txt"></i><span class="sMark">英超赛季前瞻</span></div>
		          <div class="tb_a">
		            <p class="pIntro">2017-2018赛季英超烽火重燃，本赛季英超依旧呈现六强争霸的趋势，穆里尼奥与瓜迪奥拉的曼市之旅能否成就宏图，温教授是否依然带领枪手稳健争四，波切蒂诺的青年军能否创造历史呢，孔蒂能否带领切尔西卫冕成功。</p>
		            <ul class="ulTxt clearfix">
		            <?php for ($j = 0; $j < 3; $j++) {
		            	foreach ($info[0][$j+1] as $val) {?>
		            	<li><span class="sStyle">[<?php echo $infotype[$j]?>]</span><a href="<?php echo $val['url']?>" target="_blank" <?php if ($val['redflag']) {?>class="red"<?php }?>><?php echo $val['title']?></a></li>
			            <?php if ($k == 1 && j == 1) {?>
			            </ul>
			            <ul class="ulTxt clearfix">
			            <?php }
			            }
		            }?>
		            </ul>
		          </div>
		        </div>
		        <!-- 赛季前瞻 -->
		        <!-- 数据统计 - 投注必备 -->
		        <div class="mod_a mt15">
		          <div class="th_a"><span class="sMark">英超数据统计 - 投注必备</span></div>
		          <div class="tb_a">
		            <table cellpadding="0" cellspacing="0" border="0" class="dataTable">
		              <tr><th>&nbsp;</th><th class="w102">所有场次</th><th class="w102">主场</th><th class="w102">客场</th></tr>
		              <tr><td><em>赢球概率</em></td><td>切尔西（79%）</td><td>热刺（89%）</td><td>切尔西（68%）</td></tr>
		              <tr><td><em>打平概率</em></td><td>曼联（39%）</td><td>曼联（37%）</td><td>西布朗（37%）</td></tr>
		              <tr><td><em>输球概率</em></td><td>桑德兰（68%）</td><td>桑德兰（58%）</td><td>赫尔城（79%）</td></tr>
		            </table>
		            <p class="pSummary"><em>总结</em>上赛季英超所有比赛场主胜概率为49.12%，可见胜平负适时相信还是可靠的；热刺赛季场均进球2.26，切尔西主场进球数为2.89球，让一球情况先可以适时考虑博胆让球胜。</p>
		            <div class="adPic"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-92/shuju-6517/topscorers/" target="_blank"><img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/yc-pic.jpg" alt="" title="" width="410" height="80"></a></div>
		          </div>
		        </div>
		        <!-- 数据统计 - 投注必备 -->
		      </div>
		      <!-- 中间 -->
		
		      <!-- 右侧 -->
		      <div class="rightCon">
		        <!-- 赛程 -->
		        <div class="mod_a tab-plugin">
		          <div class="th_a"><span class="sMark">赛程</span><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-92/" target="_blank" class="aMore">全部赛程></a></div>
		          <div class="tb_a">
		            <div class="matchTab">
		            	<?php foreach ($schedule[0]['schedule'] as $k => $val) {?>
		            	<a href="javascript:void(0)" target="_self" class=" <?php if ($k == $schedule[0]['current']){?>cur<?php }?> tab-plugin-tab" >第<?php echo $k?>轮</a>
		            	<?php }?>
		              <i class="iLine"></i>
		            </div>
		            <?php foreach ($schedule[0]['schedule'] as $k => $val) {?>
			            <div class="matchList tab-plugin-con" <?php if ($k == $schedule[0]['current']){?>style="display:block"<?php }?>>
			              <ul class="matchData clearfix">
			                <li class="li_th"><span class="sTime">比赛时间</span><span class="sName"><em class="emNameLeft">主队</em><em class="emVs">VS</em><em class="emNameRight">客队</em></span></li>
			                <?php foreach ($val as $v) {?>
				                <li>
				                  <span class="sTime"><?php echo date('m-d H:i', $v['mtime'])?></span>
				                  <span class="sName"><em class="emNameLeft"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['htid']?>" target="_blank"><?php echo $v['home']?></a></em>
				                  <?php if ($v['hs'] === '') {?><em class="emVs">VS</em><?php }else {?><em class="emScore"><?php echo $v['hs']?>:<?php echo $v['as']?></em><?php }?>
				                  <em class="emNameRight"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['atid']?>" target="_blank"><?php echo $v['away']?></a></em></span>
				                </li>
			                <?php }?>
			              </ul>
			            </div>
		            <?php }?>
		          </div>
		        </div>
		        <!-- 赛程 -->
		        
		        <!-- 球队巡礼 -->
		        <div class="mod_a teamList">
		          <div class="th_a"><span class="sMark">英超球队巡礼</span></div>
		          <div class="tb_a">
		            <p class="pTeamPic clearfix">
		            <?php foreach ($teams[0] as $k => $val) {?>
		            	<a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $k?>" target="_blank">
		            		<img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/logo/<?php echo $k?>.jpg" alt="" title="" width="36" height="36"><span class="sName"><?php echo $val?></span>
		            	</a>
		            <?php }?>
		            </p>
		          </div>
		        </div>
		        <!-- 球队巡礼 -->
		      </div>
		      <!-- 右侧 -->
		      <div class="clear"></div>
		    </div>
		
		    <div class="width1000 tab-plugin-extend-con">
		      <!-- 左侧 -->
		      <div class="leftCon">
		        <div class="mod_a">
		          <div class="th_a"><span class="sMark">西甲积分榜</span></div>
		          <div class="tb_a">
		            <!-- 排行榜 -->
		            <ul class="ulRank clearfix">
		              <li class="li_th"> <span class="sNum">排名</span><span class="sName">队伍</span><span class="sData">胜/平/负</span><span class="sScore">积分</span></li>
		              <?php foreach ($score[1] as $k => $scr) {?>
		              <li <?php if ($k >= count($score[1]) - 3) {?>class="li_demote"<?php }?>>
		                <span class="sNum"><i class="iNum <?php if ($k < 6){?>iRed<?php }?>"><?php echo $k+1?></i></span>
		                <span class="sName"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $scr['tid']?>" target="_blank" class="<?php if ($k < 4) {?>red<?php }else if (in_array($k, array(4, 5))){?>blue<?php }?>"><?php echo $scr['name']?></a></span>
		                <span class="sData"><?php echo $scr['w']?>/<?php echo $scr['d']?>/<?php echo $scr['l']?></span>
		                <span class="sScore"><?php echo $scr['score']?></span>
		              </li>
		              <?php }?>
		            </ul>
		            <p class="pRankTips clearfix"><em>注：</em><span>红名可参加欧冠，蓝名可参加欧联后三名在降级区</span></p>
		            <!-- 排行榜 -->
		          </div>
		        </div>
		      </div>
		      <!-- 左侧 -->
		
		      <!-- 中间 -->
		      <div class="middleCon">
		        <!-- 赛季前瞻 -->
		        <div class="mod_a">
		          <div class="th_a"><i class="icon_txt"></i><span class="sMark">西甲赛季前瞻</span></div>
		          <div class="tb_a">
		            <p class="pIntro">2017-2018赛季的西甲大体局势仍会是三强争霸，作为五大联赛之首西甲看点满满：本赛季没有了MSN的巴萨能否继续和皇马势均力敌呢；梅罗之争仍在继续，且看且珍惜；马竞入住大都会球场后能否一改以往境况力压两大宿敌呢。</p>
		            <ul class="ulTxt clearfix">
		            <?php for ($j = 0; $j < 3; $j++) {
		            	foreach ($info[1][$j+1] as $val) {?>
		            	<li><span class="sStyle">[<?php echo $infotype[$j]?>]</span><a href="<?php echo $val['url']?>" target="_blank" <?php if ($val['redflag']) {?>class="red"<?php }?>><?php echo $val['title']?></a></li>
			            <?php if ($k == 1 && j == 1) {?>
			            </ul>
			            <ul class="ulTxt clearfix">
			            <?php }
			            }
		            }?>
		            </ul>
		          </div>
		        </div>
		        <!-- 赛季前瞻 -->
		
		        <!-- 数据统计 - 投注必备 -->
		        <div class="mod_a mt15">
		          <div class="th_a"><span class="sMark">西甲数据统计 - 投注必备</span></div>
		          <div class="tb_a">
		            <table cellpadding="0" cellspacing="0" border="0" class="dataTable">
		              <tr><th>&nbsp;</th><th class="w102">所有场次</th><th class="w102">主场</th><th class="w102">客场</th></tr>
		              <tr><td><em>赢球概率</em></td><td>皇马（79%）</td><td>巴萨（79%）</td><td>皇马（79%）</td></tr>
		              <tr><td><em>打平概率</em></td><td>阿拉维斯（34%）</td><td>阿拉维斯（42%）</td><td>马拉加（42%）</td></tr>
		              <tr><td><em>输球概率</em></td><td>格拉纳达（68%）</td><td>格拉纳达（58%）</td><td>拉斯帕（79%）</td></tr>
		            </table>
		            <p class="pSummary"><em>总结</em>巴萨赛季场均进球3球以上，投注进球彩和让球时值得关注，皇马作为客场最稳定球队客胜可信，马竞攻守能力均衡，对阵弱队时冷门难出。</p>
		            <div class="adPic"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-85/shuju-6605/topscorers/" target="_blank"><img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/xj-pic.jpg" alt="" title="" width="410" height="80"></a></div>
		          </div>
		        </div>
		        <!-- 数据统计 - 投注必备 -->
		      </div>
		      <!-- 中间 -->
		
		      <!-- 右侧 -->
		      <div class="rightCon">
		        <!-- 赛程 -->
		        <div class="mod_a tab-plugin">
		          <div class="th_a"><span class="sMark">赛程</span><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-85/" target="_blank" class="aMore">全部赛程></a></div>
		          <div class="tb_a">
		            <div class="matchTab">
		            	<?php foreach ($schedule[1]['schedule'] as $k => $val) {?>
		            	<a href="javascript:void(0)" target="_self" class=" <?php if ($k == $schedule[1]['current']){?>cur<?php }?> tab-plugin-tab" >第<?php echo $k?>轮</a>
		            	<?php }?>
		              <i class="iLine"></i>
		            </div>
		            <?php foreach ($schedule[1]['schedule'] as $k => $val) {?>
			            <div class="matchList tab-plugin-con" <?php if ($k == $schedule[1]['current']){?>style="display:block"<?php }?>>
			              <ul class="matchData clearfix">
			                <li class="li_th"><span class="sTime">比赛时间</span><span class="sName"><em class="emNameLeft">主队</em><em class="emVs">VS</em><em class="emNameRight">客队</em></span></li>
			                <?php foreach ($val as $v) {?>
				                <li>
				                  <span class="sTime"><?php echo date('m-d H:i', $v['mtime'])?></span>
				                  <span class="sName"><em class="emNameLeft"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['htid']?>" target="_blank"><?php echo $v['home']?></a></em>
				                  <?php if ($v['hs'] === '') {?><em class="emVs">VS</em><?php }else {?><em class="emScore"><?php echo $v['hs']?>:<?php echo $v['as']?></em><?php }?>
				                  <em class="emNameRight"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['atid']?>" target="_blank"><?php echo $v['away']?></a></em></span>
				                </li>
			                <?php }?>
			              </ul>
			            </div>
		            <?php }?>
		          </div>
		        </div>
		        <!-- 赛程 -->
		
		        <!-- 球队巡礼 -->
		        <div class="mod_a teamList">
		          <div class="th_a"><span class="sMark">西甲球队巡礼</span></div>
		          <div class="tb_a">
		            <p class="pTeamPic clearfix">
		              <?php foreach ($teams[1] as $k => $val) {?>
		            	<a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $k?>" target="_blank">
		            		<img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/logo/<?php echo $k?>.jpg" alt="" title="" width="36" height="36"><span class="sName"><?php echo $val?></span>
		            	</a>
		              <?php }?>
		            </p>
		          </div>
		        </div>
		        <!-- 球队巡礼 -->
		      </div>
		      <!-- 右侧 -->
		      <div class="clear"></div>
		    </div>
		
		    <div class="width1000 tab-plugin-extend-con">
		      <!-- 左侧 -->
		      <div class="leftCon">
		        <div class="mod_a">
		          <div class="th_a"><span class="sMark">德甲积分榜</span></div>
		          <div class="tb_a">
		            <!-- 排行榜 -->
		            <ul class="ulRank clearfix">
		              <li class="li_th"> <span class="sNum">排名</span><span class="sName">队伍</span><span class="sData">胜/平/负</span><span class="sScore">积分</span></li>
		              <?php foreach ($score[2] as $k => $scr) {?>
		              <li <?php if ($k >= count($score[2]) - 3) {?>class="li_demote"<?php }?>>
		                <span class="sNum"><i class="iNum <?php if ($k < 6){?>iRed<?php }?>"><?php echo $k+1?></i></span>
		                <span class="sName"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $scr['tid']?>" target="_blank" class="<?php if ($k < 4) {?>red<?php }else if (in_array($k, array(4, 5))){?>blue<?php }?>"><?php echo $scr['name']?></a></span>
		                <span class="sData"><?php echo $scr['w']?>/<?php echo $scr['d']?>/<?php echo $scr['l']?></span>
		                <span class="sScore"><?php echo $scr['score']?></span>
		              </li>
		              <?php }?>
		            </ul>
		            <p class="pRankTips clearfix"><em>注：</em><span>红名可参加欧冠，蓝名可参加欧联后三名在降级区</span></p>
		            <!-- 排行榜 -->
		          </div>
		        </div>
		      </div>
		      <!-- 左侧 -->
		
		      <!-- 中间 -->
		      <div class="middleCon">
		        <!-- 赛季前瞻 -->
		        <div class="mod_a">
		          <div class="th_a"><i class="icon_txt"></i><span class="sMark">德甲赛季前瞻</span></div>
		          <div class="tb_a">
		            <p class="pIntro">2017-2018赛季德甲将于8月19日打响，班霸拜仁能否在联赛继续一骑绝尘，成就联赛6连冠霸业呢；面对宿敌多特蒙德能否继续保持竞争力与拜仁一争高下？黑马莱比锡能否再有惊喜，门兴，沙尔克04，勒沃库森暗流涌，渴望在新赛季一展宏图！</p>
		            <ul class="ulTxt clearfix">
		            <?php for ($j = 0; $j < 3; $j++) {
		            	foreach ($info[2][$j+1] as $val) {?>
		            	<li><span class="sStyle">[<?php echo $infotype[$j]?>]</span><a href="<?php echo $val['url']?>" target="_blank" <?php if ($val['redflag']) {?>class="red"<?php }?>><?php echo $val['title']?></a></li>
			            <?php if ($k == 1 && j == 1) {?>
			            </ul>
			            <ul class="ulTxt clearfix">
			            <?php }
			            }
		            }?>
		            </ul>
		          </div>
		        </div>
		        <!-- 赛季前瞻 -->
		
		        <!-- 数据统计 - 投注必备 -->
		        <div class="mod_a mt15">
		          <div class="th_a"><span class="sMark">德甲数据统计 - 投注必备</span></div>
		          <div class="tb_a">
		            <table cellpadding="0" cellspacing="0" border="0" class="dataTable">
		              <tr><th>&nbsp;</th><th class="w102">所有场次</th><th class="w102">主场</th><th class="w102">客场</th></tr>
		              <tr><td><em>赢球概率</em></td><td>拜仁（74%）</td><td>拜仁（76%）</td><td>拜仁（71%）</td></tr>
		              <tr><td><em>打平概率</em></td><td>霍芬海姆（41%）</td><td>法兰克福（41%）</td><td>霍芬海姆（47%）</td></tr>
		              <tr><td><em>输球概率</em></td><td>达姆施（68%）</td><td>狼堡（53%）</td><td>达姆施（88%）</td></tr>
		            </table>
		            <p class="pSummary"><em>总结</em>进攻和防守数据方面拜仁独占鳌头，本赛季拜仁依旧实力超群值得看好；多特主场赢球概率和拜仁不分秋色，霍芬海姆主场赛季不败，可见主场值得相信。</p>
		            <div class="adPic"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-39/shuju-6563/topscorers/" target="_blank"><img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/dj-pic.jpg" alt="" title="" width="410" height="80"></a></div>
		          </div>
		        </div>
		        <!-- 数据统计 - 投注必备 -->
		      </div>
		      <!-- 中间 -->
		
		      <!-- 右侧 -->
		      <div class="rightCon">
		        <!-- 赛程 -->
		        <div class="mod_a tab-plugin">
		          <div class="th_a"><span class="sMark">赛程</span><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-39/" target="_blank" class="aMore">全部赛程></a></div>
		          <div class="tb_a">
		            <div class="matchTab">
		            	<?php foreach ($schedule[2]['schedule'] as $k => $val) {?>
		            	<a href="javascript:void(0)" target="_self" class="<?php if ($k == $schedule[2]['current']){?>cur<?php }?> tab-plugin-tab" >第<?php echo $k?>轮</a>
		            	<?php }?>
		              <i class="iLine"></i>
		            </div>
		            <?php foreach ($schedule[2]['schedule'] as $k => $val) {?>
			            <div class="matchList tab-plugin-con" <?php if ($k == $schedule[2]['current']){?>style="display:block"<?php }?>>
			              <ul class="matchData clearfix">
			                <li class="li_th"><span class="sTime">比赛时间</span><span class="sName"><em class="emNameLeft">主队</em><em class="emVs">VS</em><em class="emNameRight">客队</em></span></li>
			                <?php foreach ($val as $v) {?>
				                <li>
				                  <span class="sTime"><?php echo date('m-d H:i', $v['mtime'])?></span>
				                  <span class="sName"><em class="emNameLeft"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['htid']?>" target="_blank"><?php echo $v['home']?></a></em>
				                  <?php if ($v['hs'] === '') {?><em class="emVs">VS</em><?php }else {?><em class="emScore"><?php echo $v['hs']?>:<?php echo $v['as']?></em><?php }?>
				                  <em class="emNameRight"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['atid']?>" target="_blank"><?php echo $v['away']?></a></em></span>
				                </li>
			                <?php }?>
			              </ul>
			            </div>
		            <?php }?>
		          </div>
		        </div>
		        <!-- 赛程 -->
		
		        <!-- 球队巡礼 -->
		        <div class="mod_a teamList">
		          <div class="th_a"><span class="sMark">德甲球队巡礼</span></div>
		          <div class="tb_a">
		            <p class="pTeamPic clearfix">
		              <?php foreach ($teams[2] as $k => $val) {?>
		            	<a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $k?>" target="_blank">
		            		<img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/logo/<?php echo $k?>.jpg" alt="" title="" width="36" height="36"><span class="sName"><?php echo $val?></span>
		            	</a>
		            <?php }?>
		            </p>
		          </div>
		        </div>
		        <!-- 球队巡礼 -->
		      </div>
		      <!-- 右侧 -->
		      <div class="clear"></div>
		    </div>
		    <div class="width1000 tab-plugin-extend-con">
		      <!-- 左侧 -->
		      <div class="leftCon">
		        <div class="mod_a">
		          <div class="th_a"><span class="sMark">意甲积分榜</span></div>
		          <div class="tb_a">
		            <!-- 排行榜 -->
		            <ul class="ulRank clearfix">
		              <li class="li_th"> <span class="sNum">排名</span><span class="sName">队伍</span><span class="sData">胜/平/负</span><span class="sScore">积分</span></li>
		              <?php foreach ($score[3] as $k => $scr) {?>
		              <li <?php if ($k >= count($score[3]) - 3) {?>class="li_demote"<?php }?>>
		                <span class="sNum"><i class="iNum <?php if ($k < 5){?>iRed<?php }?>"><?php echo $k+1?></i></span>
		                <span class="sName"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $scr['tid']?>" target="_blank" class="<?php if ($k < 3) {?>red<?php }else if (in_array($k, array(3, 4))){?>blue<?php }?>"><?php echo $scr['name']?></a></span>
		                <span class="sData"><?php echo $scr['w']?>/<?php echo $scr['d']?>/<?php echo $scr['l']?></span>
		                <span class="sScore"><?php echo $scr['score']?></span>
		              </li>
		              <?php }?>
		            </ul>
		            <p class="pRankTips clearfix"><em>注：</em><span>红名可参加欧冠，蓝名可参加欧联后三名在降级区</span></p>
		            <!-- 排行榜 -->
		          </div>
		        </div>
		      </div>
		      <!-- 左侧 -->
		
		      <!-- 中间 -->
		      <div class="middleCon">
		        <!-- 赛季前瞻 -->
		        <div class="mod_a">
		          <div class="th_a"><i class="icon_txt"></i><span class="sMark">意甲赛季前瞻</span></div>
		          <div class="tb_a">
		            <p class="pIntro">2017-2018赛季意甲揭开帷幕，班霸尤文图斯连冠记录能否改写，米兰双雄在获得中资资本后纷纷补强，尤其AC米兰在今夏狂掷2亿欧，复兴之路值得期待。本赛季将取消冬歇期，严密的赛程之下究竟那几支队伍可以脱颖而出？</p>
		            <ul class="ulTxt clearfix">
		            <?php for ($j = 0; $j < 3; $j++) {
		            	foreach ($info[3][$j+1] as $val) {?>
		            	<li><span class="sStyle">[<?php echo $infotype[$j]?>]</span><a href="<?php echo $val['url']?>" target="_blank" <?php if ($val['redflag']) {?>class="red"<?php }?>><?php echo $val['title']?></a></li>
			            <?php if ($k == 1 && j == 1) {?>
			            </ul>
			            <ul class="ulTxt clearfix">
			            <?php }
			            }
		            }?>
		            </ul>
		          </div>
		        </div>
		        <!-- 赛季前瞻 -->
		
		        <!-- 数据统计 - 投注必备 -->
		        <div class="mod_a mt15">
		          <div class="th_a"><span class="sMark">意甲数据统计 - 投注必备</span></div>
		          <div class="tb_a">
		            <table cellpadding="0" cellspacing="0" border="0" class="dataTable">
		              <tr><th>&nbsp;</th><th class="w102">所有场次</th><th class="w102">主场</th><th class="w102">客场</th></tr>
		              <tr><td><em>赢球概率</em></td><td>尤文（76%）</td><td>尤文（95%）</td><td>那不勒斯（68%）</td></tr>
		              <tr><td><em>打平概率</em></td><td>都灵（37%）</td><td>佛罗伦萨（42%）</td><td>AC米兰（37%）</td></tr>
		              <tr><td><em>输球概率</em></td><td>佩斯卡拉（68%）</td><td>佩斯卡拉（63%）</td><td>佩斯卡拉（74%）</td></tr>
		            </table>
		            <p class="pSummary"><em>总结</em>尤文主场胜率达到了恐怖的95%，那不勒斯和罗马攻击力出众更是在尤文之上，本赛季有一争之力，在各方补强后的国米，AC米兰实力亦不容小觑！</p>
		            <div class="adPic"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-34/shuju-6643/topscorers/" target="_blank"><img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/yj-pic.jpg" alt="" title="" width="410" height="80"></a></div>
		          </div>
		        </div>
		        <!-- 数据统计 - 投注必备 -->
		      </div>
		      <!-- 中间 -->
		
		      <!-- 右侧 -->
		      <div class="rightCon">
		        <!-- 赛程 -->
		        <div class="mod_a tab-plugin">
		          <div class="th_a"><span class="sMark">赛程</span><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-34/" target="_blank" class="aMore">全部赛程></a></div>
		          <div class="tb_a">
		            <div class="matchTab">
		            	<?php foreach ($schedule[3]['schedule'] as $k => $val) {?>
		            	<a href="javascript:void(0)" target="_self" class="<?php if ($k == $schedule[3]['current']){?>cur<?php }?> tab-plugin-tab" >第<?php echo $k?>轮</a>
		            	<?php }?>
		              <i class="iLine"></i>
		            </div>
		            <?php foreach ($schedule[3]['schedule'] as $k => $val) {?>
			            <div class="matchList tab-plugin-con" <?php if ($k == $schedule[3]['current']){?>style="display:block"<?php }?>>
			              <ul class="matchData clearfix">
			                <li class="li_th"><span class="sTime">比赛时间</span><span class="sName"><em class="emNameLeft">主队</em><em class="emVs">VS</em><em class="emNameRight">客队</em></span></li>
			                <?php foreach ($val as $v) {?>
				                <li>
				                  <span class="sTime"><?php echo date('m-d H:i', $v['mtime'])?></span>
				                  <span class="sName"><em class="emNameLeft"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['htid']?>" target="_blank"><?php echo $v['home']?></a></em>
				                  <?php if ($v['hs'] === '') {?><em class="emVs">VS</em><?php }else {?><em class="emScore"><?php echo $v['hs']?>:<?php echo $v['as']?></em><?php }?>
				                  <em class="emNameRight"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['atid']?>" target="_blank"><?php echo $v['away']?></a></em></span>
				                </li>
			                <?php }?>
			              </ul>
			            </div>
		            <?php }?>
		          </div>
		        </div>
		        <!-- 赛程 -->
		
		        <!-- 球队巡礼 -->
		        <div class="mod_a teamList">
		          <div class="th_a"><span class="sMark">意甲球队巡礼</span></div>
		          <div class="tb_a">
		            <p class="pTeamPic clearfix">
		              <?php foreach ($teams[3] as $k => $val) {?>
		            	<a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $k?>" target="_blank">
		            		<img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/logo/<?php echo $k?>.jpg" alt="" title="" width="36" height="36"><span class="sName"><?php echo $val?></span>
		            	</a>
		              <?php }?>
		            </p>
		          </div>
		        </div>
		        <!-- 球队巡礼 -->
		      </div>
		      <!-- 右侧 -->
		      <div class="clear"></div>
		    </div>
		
		    <div class="width1000 tab-plugin-extend-con">
		      <!-- 左侧 -->
		      <div class="leftCon">
		        <div class="mod_a">
		          <div class="th_a"><span class="sMark">法甲积分榜</span></div>
		          <div class="tb_a">
		            <!-- 排行榜 -->
		            <ul class="ulRank clearfix">
		              <li class="li_th"> <span class="sNum">排名</span><span class="sName">队伍</span><span class="sData">胜/平/负</span><span class="sScore">积分</span></li>
		              <?php foreach ($score[4] as $k => $scr) {?>
		              <li <?php if ($k >= count($score[4]) - 3) {?>class="li_demote"<?php }?>>
		                <span class="sNum"><i class="iNum <?php if ($k < 4){?>iRed<?php }?>"><?php echo $k+1?></i></span>
		                <span class="sName"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $scr['tid']?>" target="_blank" class="<?php if ($k < 3) {?>red<?php }else if ($k == 3){?>blue<?php }?>"><?php echo $scr['name']?></a></span>
		                <span class="sData"><?php echo $scr['w']?>/<?php echo $scr['d']?>/<?php echo $scr['l']?></span>
		                <span class="sScore"><?php echo $scr['score']?></span>
		              </li>
		              <?php }?>
		            </ul>
		            <p class="pRankTips clearfix"><em>注：</em><span>红名可参加欧冠，蓝名可参加欧联后三名在降级区</span></p>
		            <!-- 排行榜 -->
		          </div>
		        </div>
		      </div>
		      <!-- 左侧 -->
		
		      <!-- 中间 -->
		      <div class="middleCon">
		        <!-- 赛季前瞻 -->
		        <div class="mod_a">
		          <div class="th_a"><i class="icon_txt"></i><span class="sMark">法甲赛季前瞻</span></div>
		          <div class="tb_a">
		            <p class="pIntro">2017-2018赛季法甲，上季冠军摩纳哥恐卫冕希望渺茫，大巴黎上赛季意外失冠，夏天重金招揽了内马尔，本赛季球队不仅要重新摘冠更是剑指欧冠，黑马尼斯能否继续异军突起，里昂，尼斯，马赛，圣埃蒂安群雄逐鹿，力争取得欧战资格。</p>
		            <ul class="ulTxt clearfix">
		            <?php for ($j = 0; $j < 3; $j++) {
		            	foreach ($info[4][$j+1] as $val) {?>
		            	<li><span class="sStyle">[<?php echo $infotype[$j]?>]</span><a href="<?php echo $val['url']?>" target="_blank" <?php if ($val['redflag']) {?>class="red"<?php }?>><?php echo $val['title']?></a></li>
			            <?php if ($k == 1 && j == 1) {?>
			            </ul>
			            <ul class="ulTxt clearfix">
			            <?php }
			            }
		            }?>
		            </ul>
		          </div>
		        </div>
		        <!-- 赛季前瞻 -->
		
		        <!-- 数据统计 - 投注必备 -->
		        <div class="mod_a mt15">
		          <div class="th_a"><span class="sMark">法甲数据统计 - 投注必备</span></div>
		          <div class="tb_a">
		            <table cellpadding="0" cellspacing="0" border="0" class="dataTable">
		              <tr><th>&nbsp;</th><th class="w102">所有场次</th><th class="w102">主场</th><th class="w102">客场</th></tr>
		              <tr><td><em>赢球概率</em></td><td>摩纳哥（79%）</td><td>摩纳哥（89%）</td><td>摩纳哥（74%）</td></tr>
		              <tr><td><em>打平概率</em></td><td>圣埃蒂安（37%）</td><td>巴斯蒂亚（53%）</td><td>尼斯（42%）</td></tr>
		              <tr><td><em>输球概率</em></td><td>洛里昂（58%）</td><td>卡昂（53%）</td><td>巴斯蒂亚（79%）</td></tr>
		            </table>
		            <p class="pSummary"><em>总结</em>上赛季摩纳哥进攻力冠绝联赛，本赛季由于人员变动较大，值得关注；尼斯攻守较为平衡，夏窗人员没有太大变动，稳重有升，值得期待！欧战资格的争夺预计摩纳哥，里昂，尼斯，马赛之间会进行的非常惨烈！</p>
		            <div class="adPic"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-93/shuju-6524/topscorers/" target="_blank"><img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/fj-pic.jpg" alt="" title="" width="410" height="80"></a></div>
		          </div>
		        </div>
		        <!-- 数据统计 - 投注必备 -->
		      </div>
		      <!-- 中间 -->
		
		      <!-- 右侧 -->
		      <div class="rightCon">
		        <!-- 赛程 -->
		        <div class="mod_a tab-plugin">
		          <div class="th_a"><span class="sMark">赛程</span><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-93/" target="_blank" class="aMore">全部赛程></a></div>
		          <div class="tb_a">
		            <div class="matchTab">
		            	<?php foreach ($schedule[4]['schedule'] as $k => $val) {?>
		            	<a href="javascript:void(0)" target="_self" class="<?php if ($k == $schedule[4]['current']){?>cur<?php }?> tab-plugin-tab" >第<?php echo $k?>轮</a>
		            	<?php }?>
		              <i class="iLine"></i>
		            </div>
		            <?php foreach ($schedule[4]['schedule'] as $k => $val) {?>
			            <div class="matchList tab-plugin-con" <?php if ($k == $schedule[4]['current']){?>style="display:block"<?php }?>>
			              <ul class="matchData clearfix">
			                <li class="li_th"><span class="sTime">比赛时间</span><span class="sName"><em class="emNameLeft">主队</em><em class="emVs">VS</em><em class="emNameRight">客队</em></span></li>
			                <?php foreach ($val as $v) {?>
				                <li>
				                  <span class="sTime"><?php echo date('m-d H:i', $v['mtime'])?></span>
				                  <span class="sName"><em class="emNameLeft"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['htid']?>" target="_blank"><?php echo $v['home']?></a></em>
				                  <?php if ($v['hs'] === '') {?><em class="emVs">VS</em><?php }else {?><em class="emScore"><?php echo $v['hs']?>:<?php echo $v['as']?></em><?php }?>
				                  <em class="emNameRight"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $v['atid']?>" target="_blank"><?php echo $v['away']?></a></em></span>
				                </li>
			                <?php }?>
			              </ul>
			            </div>
		            <?php }?>
		          </div>
		        </div>
		        <!-- 赛程 -->
		
		        <!-- 球队巡礼 -->
		        <div class="mod_a teamList">
		          <div class="th_a"><span class="sMark">法甲球队巡礼</span></div>
		          <div class="tb_a">
		            <p class="pTeamPic clearfix">
		              <?php foreach ($teams[4] as $k => $val) {?>
		            	<a rel="nofollow" href="<?php echo $this->config->item('api_info')?>teams/<?php echo $k?>" target="_blank">
		            		<img src="/caipiaoimg/v1.1/img/active/fiveLeagueInformation/logo/<?php echo $k?>.jpg" alt="" title="" width="36" height="36"><span class="sName"><?php echo $val?></span>
		            	</a>
		            <?php }?>
		            </p>
		          </div>
		        </div>
		        <!-- 球队巡礼 -->
		      </div>
		      <!-- 右侧 -->
		      <div class="clear"></div>
		    </div>
		  </div>
		</div>
		
		<script type="text/javascript">
		  $(function(){
		    if($(".tab-plugin").length > 0){
		      $(".tab-plugin").each(function(){
		        var pluginTabArr = $(this).find(".tab-plugin-tab"),pluginConArr = $(this).find(".tab-plugin-con");
		        pluginTabArr.bind("mouseenter",function(){
		          pluginConArr.css({"display":"none"});
		          pluginTabArr.removeClass("cur");
		          pluginConArr.eq(pluginTabArr.index($(this))).css({"display":"block"});
		          $(this).addClass("cur");
		        })
		      })
		    }
		    if($(".tab-plugin-extend").length > 0){
		      $(".tab-plugin-extend").each(function(){
		        var pluginTabArr = $(this).find(".tab-plugin-extend-tab"),pluginConArr = $(this).find(".tab-plugin-extend-con");
		        pluginTabArr.bind("click",function(){
		          pluginConArr.css({"display":"none"});
		          pluginTabArr.removeClass("cur");
		          pluginConArr.eq(pluginTabArr.index($(this))).css({"display":"block"});
		          $(this).addClass("cur");
		        })
		      })
		    }
		  })
		  $(".rightScroll .aClose").click(function(){
			  $(".rightScroll").hide();
		  })
		</script>
		<?php $this->load->view('v1.1/elements/common/footer_academy');?>
	</body>
</html>