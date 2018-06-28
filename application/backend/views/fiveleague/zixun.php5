<link rel="stylesheet" href="/caipiaoimg/v1.1/styles/global.min.css">
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
		.menu a:hover,.menu a.cur { text-decoration: none; color: #27c55f}
		.menu a.cur { border-bottom: 4px solid #27c55f; padding-bottom: 0;}
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
		
		.red { color: #e14949;}
		.red:hover { color: #e14949;}
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
		.matchData li .sName .emVs { padding: 0 7px; color: #999; font-size: 12px;}
		.matchData li.li_th { color: #999; padding-top: 10px; line-height: 28px; height: 28px; border-top: 0 none;}
		.matchData li.li_th span { color: #999;}
		
		.pTeamPic { padding: 1px 0 0 1px; overflow: hidden;}
		.pTeamPic a { float: left; border: 1px solid #dddddd; text-align: center; width: 58px; margin: -1px 0 0 -1px; display: inline; position: relative; z-index: 1;}
		.pTeamPic a img { display: block; width: 36px; height: 36px; vertical-align: top; padding-top: 5px; margin: 0 auto;}
		.pTeamPic a .sName { line-height: 18px; height: 18px; display: block; font-size: 12px; overflow: hidden;}
		.pTeamPic a:hover{ border-color: #f37c26; z-index: 5;}
		
		.teamList{ margin-top: 13px;}
		</style>
			<div class="middleCon">
		        <!-- 赛季前瞻 -->
		        <div class="mod_a">
		          <div class="th_a">
		            <i class="icon_txt"></i>
		            <span class="sMark"><?php echo $team?>赛季前瞻</span>
		          </div>
		          <div class="tb_a">
		            <p class="pIntro">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</p>
		            <ul class="ulTxt clearfix">
		            <?php for ($j = 0; $j < 3; $j++) {
		            	foreach ($info[$j] as $val) {?>
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
		      </div>