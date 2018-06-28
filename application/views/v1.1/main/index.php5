    <!DOCTYPE HTML>
    <html>
        <head>
            <meta charset="utf-8">
            <title>166彩票官网-双色球，大乐透，老11选5，竞彩足球彩票服务开奖平台</title>
            <meta content="166彩票官网作为彩民首选的100%安全预约服务平台，提供双色球，超级大乐透，新11选5，七星彩，福彩3D，老11选5，竞彩足球，竞彩篮球等多热门彩种服务合买定制跟单平台！" name="Description" />
            <meta content="166彩票，166彩票官网，双色球，竞彩足球，竞彩篮球，大乐透，七星彩，福彩3D，老11选5，胜负彩，大乐透，福彩3D，彩票合买，单式上传，复制粘贴，定制跟单" name="Keywords" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <meta name="baidu-site-verification" content="lQnvYyQA6s" />
            <base href="<?php echo $this->config->item('pages_url')?>" target="_blank">
            <meta name="mobile-agent" content="format=html5;url=<?php echo $this->config->item('m_pages_url')?>" />
            <meta http-equiv="Cache-Control" content="no-siteapp" />
            <meta http-equiv="Cache-Control" content="no-transform" />
            <!--[if lte IE 6]></base><![endif]-->
            <link rel="shortcut icon" href="/favicon.ico"/>
            <link rel="bookmark" href="/favicon.ico"/>
            <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>"/>
            <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
            <link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/index.min.css');?>" rel="stylesheet"/>
        </head>
        <body>
            <script type="text/javascript">
                var baseUrl = '<?php echo $this->config->item('pages_url')?>';
                var uri = '<?php echo str_replace(array('<', '>', 'script'), '', $_SERVER['REQUEST_URI']);?>';
                var version = 'v1.1';
                var G = {
                	baseUrl: baseUrl
                }
                var visitor = {userNickname:'<?php echo empty($this->uid) ? '未登录用户' : $this->uinfo['uname']?>'};
				window.easemobim = window.easemobim || {};
				easemobim.config = {visitor: visitor};
            </script>
            <script src='//kefu.easemob.com/webim/easemob.js'></script>
 		<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
            <?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
            <!--header begin-->
            <div class="header">
			  <div class="wrap header-inner">
			    <div class="logo">
			    	<h1 class="logo-txt"><span class="logo-txt-name">166彩票</span><span class="logo-txt-slogan">为生活添彩</span></h1>
			    	<a href="/" target="_self" class="logo-img"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166.png');?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166@2x.svg');?> 2x" width="280" height="70" alt="166彩票网"></a>
		    	<a href="/activity/welcometo166" class="logo-active"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/logo/logo-active.png');?>" width="194" height="70" alt="你预约，我护航"></a>
			    </div>
			    <div class="aside">
			    	<a href="javascript:;" onclick="easemobim.bind({tenantId: '38338'})" target="_self" class="btn online-service"><i class="icon-font">&#xe634;</i>在线客服</a>
			    	<p class="telphone"><i class="icon-font">&#xe633;</i>400-690-6760</p>
			    </div>
			  </div>
			</div>
            <!--header end-->
		<div class="p-index">
            <!--nav begin-->
            <div class="nav">
			  <div class="wrap_in">
			  <div class="nav-ticket" con = 'main'>全部彩种
		      <div class="lottery-categorys" style="display: block">
		        <ul class="lottery-categorys-a">
		          <li>
		            <a href="<?php echo $baseUrl; ?>ssq" class="item-a nav-ssq">
		              <div class="lottery-img">
		                <svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg>
		              </div>
		              <p class="cont"><span class="title"><strong>双色球</strong><s>2元可中1000万</s></span></p>
		            </a>
		          </li>
		          <li>
		            <a href="<?php echo $baseUrl; ?>dlt" class="item-a nav-dlt">
		              <div class="lottery-img">
		                <svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg>
		              </div>
		              <p class="cont"><span class="title"><strong>大乐透</strong></span></p>
		            </a>
		            <a href="/dlt" class="arrow-tag nav-dlt-tag"><?php if($dltPool){ echo $dltPool. '亿奖池';}else{ echo '3元可中1600万';}?></a>
		          </li>
		          <li>
		            <a href="<?php echo $baseUrl; ?>jczq/hh" class="item-a nav-jczq">
		              <div class="lottery-img">
		                <svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg>
		              </div>
		              <p class="cont"><span class="title"><strong>竞彩足球</strong></span></p></a>
                  <a href="jczq/dg" class="arrow-tag nav-jclq-tag">单关固赔</a>
		          </li>
		          <li>
		            <a href="<?php echo $baseUrl; ?>jclq/hh" class="item-a nav-jclq">
		              <div class="lottery-img">
		                <svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg>
		              </div>
		              <p class="cont"><span class="title"><strong>竞彩篮球</strong><s>69%返奖率</s></span></p>
		            </a>
		          </li>
		          <li>
		              <a href="<?php echo $baseUrl; ?>jxsyxw" class="item-a nav-jxsyxw">
			              <div class="lottery-img"><svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg></div>
			              <p class="cont"><span class="title"><strong>新11选5</strong></span></p>
			          </a>
			          <a href="<?php echo $baseUrl; ?>jxsyxw" class="arrow-tag nav-syxw-tag" style="left:115px;">热卖中...</a>
                  </li>
		          <li>
		            <a href="<?php echo $baseUrl; ?>ks" class="item-a nav-ks">
		              <div class="lottery-img">
		                <svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg>
		              </div>
		              <p class="cont"><span class="title"><strong>经典快3</strong></span></p>
		            </a>
		            <a href="<?php echo $baseUrl; ?>ks" class="arrow-tag nav-jclq-tag">摇骰子赢大奖</a>
		          </li>
		        </ul>
		        <ul class="lottery-categorys-b">
		          <li class="other">
		              <p>数字彩</p>
		              <div class="tlist-m">
		                  <a href="<?php echo $baseUrl; ?>fcsd">福彩3D</a><a href="<?php echo $baseUrl; ?>qlc">七乐彩</a><a href="<?php echo $baseUrl; ?>pls">排列三</a><a href="<?php echo $baseUrl; ?>qxc">七星彩</a><a href="<?php echo $baseUrl; ?>plw">排列五</a>
		              </div>
		          </li>
		          <li class="other has-hideitem">
		              <p>高频彩</p>
		              <div class="tlist-m tlist-m-l">
		                  <a href="<?php echo $baseUrl; ?>hbsyxw" style="margin-right: 0px;padding-right: 0px;">惊喜11选5</a>
		                  <a href="<?php echo $baseUrl; ?>syxw">老11选5<span class="arrow-tag nav-syxw-tag">热</span></a>
		                  <a href="<?php echo $baseUrl; ?>jxks" class="main-color-s">红快3</a>
		                  <a href="<?php echo $baseUrl; ?>jlks" class="main-color-s">易快3<span class="arrow-tag nav-syxw-tag">火热</span></a>
		              </div>
		              <div class="hover-item" style="width:180px"><a href="<?php echo $baseUrl; ?>klpk">快乐扑克<span class="arrow-tag">停售</span></a><a href="<?php echo $baseUrl; ?>gdsyxw">乐11选5<span class="arrow-tag">人气最旺</span></a></div>
		          </li>
		          <li class="other last"><p>竞技彩</p><div class="tlist-m"><a href="<?php echo $baseUrl; ?>sfc">胜负彩</a><a href="<?php echo $baseUrl; ?>rj">任选九</a><a href="<?php echo $baseUrl; ?>gjc">冠军彩</a><a href="<?php echo $baseUrl; ?>gjc/gyj">冠亚军彩</a></div></li>
		        </ul>
		      </div>
		    </div>
		    <ul class="nav-main clearfix">
		      <li class="<?php echo $this->con == 'main' ? 'cur' : ''; ?>"><a target="_self" href="/">首页</a></li>
		      <li class="<?php echo $this->con == 'hall' ? 'cur' : ''; ?>"><a target="_self" href="/hall">彩票大厅</a></li>
		      <li class="<?php echo ($this->con == 'hemai' || $this->con == 'gendan') ? 'cur' : ''; ?>"><a target="_blank" href="/hemai">合买<i class="icon-font icon-arrow">&#xe62a;</i></a>
                        <div class="nav-main-list"><ul><li><a target="_blank" href="/gendan" target="_blank">跟单</a></li></ul></div>
                      </li>
		      <li class="<?php echo $this->con == 'kaijiang' ? 'cur' : ''; ?>">
		     	 <a href="/kaijiang">全国开奖<i class="icon-font icon-arrow">&#xe62a;</i></a>
		     	 <div class="nav-main-list"><ul><li><a href="/kaijiang/ssq" target="_blank">开奖详情</a></li></ul></div>
		      </li>
		      <li class="<?php echo $this->con == 'chart' ? 'cur' : ''; ?>"><a target="_self" href="/chart">走势图</a></li>
		      <li>
		        <a target="_blank" href="/info">资讯中心<i class="icon-font icon-arrow">&#xe62a;</i></a>
		        <div class="nav-main-list"><ul><li><a href="/academy" target="_blank">彩票学院</a></li><li><a href="/activity/fiveLeague" target="_blank">五大联赛</a></li></ul></div>
		      </li>
		      <li>
		        <a target="_blank" rel="nofollow" href="<?php echo $this->config->item('api_info')?>">赛事数据<i class="icon-font icon-arrow">&#xe62a;</i></a>
		        <div class="nav-main-list">
		          <ul>
		            <li><a rel="nofollow" href="<?php echo $this->config->item('api_bf')?>jingcai" target="_blank">比分直播</a></li><li><a rel="nofollow" href="<?php echo $this->config->item('api_odds')?>complex/jc" target="_blank">赔率中心</a></li>
		            <li><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>" target="_blank">足球数据</a></li><li><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>lanqiu" target="_blank">篮球数据</a></li>
		          </ul>
		        </div>
		      </li>
		      <li class="nav-phone <?php echo $this->con == 'phone' ? 'cur' : ''; ?>">
		        <a href="/activity/newmode" target="_blank">
		         <i class="icon-font">&#xe61c;</i> 手机彩票<div class="qrcode"><h2>免费下载手机客户端</h2><p>彩店出票 领奖无忧</p><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/qrcode.png');?>" width="94" height="94" alt=""></div>
			        </a>
			      </li>
			    </ul>
			  </div>
			</div>
            <!--nav end-->
  <div class="slide">
    <ul class="slide-list conList">
    <?php foreach ($jingtai['banner'] as $banner) {?>
      <li class="con" style="background: <?php echo '#'.$banner['bgcolor']?>;">
        <a href="<?php echo $banner['url']?>" target="_blank">
          <img src="uploads/shouyebanner/<?php echo $banner['path']?>" width="1000" height="320" alt="<?php echo $banner['title']?>" />
        </a>
      </li>
    <?php }?>
    </ul>
    <span class="slide-btn slide-btn-l"><i class="slide-arrow icon-font">&#xe623;</i></span>
    <span class="slide-btn slide-btn-r"><i class="slide-arrow icon-font">&#xe629;</i></span>
    <div class="slide-num"><span class="slide-num-inner"></span></div>
  </div>
  <div class="wrap_in">
    <div class="os-mod">
      <div class="os-mod-l article-center">
        <div class="article-center-inner">
          <ul class="mod-tab clearfix"><li class="current"><a href="/notice/index">网站公告</a></li><li><a href="/help/index/b4-i1">帮助中心</a></li></ul>
          <div class="mod-tab-con">
          	<div class="mod-tab-item" style="display: block;">
              <ul>
	          	<?php
				if ($noticeInfo) {
					foreach ($noticeInfo as $v) {?>
					<li><a target="_blank" href="<?php echo $baseUrl; ?>notice/detail/<?php echo $v['id']; ?>"><?php echo $v['title']; ?></a></li>
					<?php }
				} ?>
				</ul>
            </div>
            <div class="mod-tab-item">
              <ul>
              	<li><a href="<?php echo $baseUrl; ?>help/index/b0-s1-f1">怎么注册166彩票帐号？</a></li>
                <li><a href="<?php echo $baseUrl; ?>help/index/b1-s1-f1">怎么给我的帐户充值？</a></li>
                <li><a href="<?php echo $baseUrl; ?>help/index/b3-s2">中奖后如何兑奖？</a></li>
              </ul>
            </div>
          </div>
          <div class="qrcode">            
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/qrcode2.png')?>" width="156" height="94" alt="">
            <p>免费下载手机客户端</p>
          </div>
        </div>
        
      </div>
      <!-- 试试手气 start -->
      <div class="os-mod-r bet-try">
        <div class="os-mod-hd">
          <h4 class="os-mod-title">试试手气</h4>
        </div>
        <div class="os-mod-bd bet-try-bd">
          <ul class="mod-tab clearfix">
            <li class="current"><a href="/ssq">双色球</a></li>
            <li><a href="/dlt">大乐透</a></li>
            <li><a href="/syxw">老11选5</a></li>
            <li><a href="/jczq/hh">竞彩足球</a></li>
          </ul>
          <div class="mod-tab-con">
            <div class="mod-tab-item sssq-ssq" data-lottery="ssq" data-playType="1" data-max="33,16" style="display:block;">
              <!-- 双色球 -->
              <div class="lnk-group">
                <a href="/ssq" class="btn-sup">自助选号</a>
                <a rel="nofollow" href="/tools/caculate/ssq" class="btn-sup">奖金计算</a>
                <a href="/kaijiang/ssq" class="btn-sup">开奖详情</a>
              </div>
              
              <div class="mod-bet">
                <div class="mod-bet-hd">
                  <span>第<?php echo $issues[SSQ]['cIssue']['seExpect']?>期</span><span></span><span>当前奖池：<em><?php if($issues[SSQ]['lIssue']['pool']['b']){ echo $issues[SSQ]['lIssue']['pool']['b'].' 亿';}?> <?php if(empty($issues[SSQ]['lIssue']['pool']['b']) && empty($issues[SSQ]['lIssue']['pool']['m'])){ echo '奖池更新中...';}else{ echo $issues[SSQ]['lIssue']['pool']['m'] . ' 万';}?></em><s><?php if ($issues[SSQ]['jrkj'] == 1) {?><u class="arrow-tag">今日开奖</u><?php }?></s></span>
                </div>
                <div class="mod-bet-bd szc">
                  <div class="mod-bet-l ball-group-b inputArea"></div>

                  <div class="mod-bet-r">
                    <a href="javascript:;" target="_self" class="change"><i class="icon-font">&#xe625;</i>换一换</a><a href="javascript:;" class="btn btn-main btn-bet" target="_self">立即预约</a>
                  </div>
                </div>
                <div class="mod-bet-ft">
                  <div class="multi-modifier-s">
                    <a href="javascript:;" target="_self" class="minus">-</a>
                    <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                    <a href="javascript:;" target="_self" class="plus" data-max="99">+</a>
                  </div>倍<span>共 <em>2</em> 元</span>
                </div>
              </div>
            </div>
            <div class="mod-tab-item sssq-dlt" data-lottery="dlt" data-playType="1" data-max="35,12">
              <!-- 大乐透 -->
              <div class="lnk-group">
                <a href="/dlt" class="btn-sup">自助选号</a>
                <a rel="nofollow" href="/tools/caculate/dlt" class="btn-sup">奖金计算</a>
                <a href="/kaijiang/dlt" class="btn-sup">开奖详情</a>
              </div>
              
              <div class="mod-bet">
                <div class="mod-bet-hd">
                  <span>第<?php echo $issues[DLT]['cIssue']['seExpect']?>期</span><span></span><span>当前奖池：<em><?php if($issues[DLT]['lIssue']['pool']['b']){ echo $issues[DLT]['lIssue']['pool']['b'].' 亿';}?> <?php if(empty($issues[DLT]['lIssue']['pool']['b']) && empty($issues[DLT]['lIssue']['pool']['m'])){ echo '奖池更新中...';}else{ echo $issues[DLT]['lIssue']['pool']['m'] . ' 万';}?></em><s><?php if ($issues[DLT]['jrkj'] == 1) {?><u class="arrow-tag">今日开奖</u><?php }?></s></span>
                </div>
                <div class="mod-bet-bd szc">
                  <div class="mod-bet-l ball-group-b inputArea"></div>

                  <div class="mod-bet-r">
                    <a href="javascript:;" target="_self" class="change"><i class="icon-font">&#xe625;</i>换一换</a><a href="javascript:;" class="btn btn-main btn-bet" target="_self">立即预约</a>
                  </div>
                </div>
                <div class="mod-bet-ft">
                  <div class="multi-modifier-s">
                    <a href="javascript:;" target="_self" class="minus">-</a>
                    <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                    <a href="javascript:;" target="_self" class="plus" data-max="99">+</a>
                  </div>倍<span>共 <em>2</em> 元</span>
                </div>
              </div>
            </div>
            <div class="mod-tab-item sssq-syxw" data-lottery="syxw" data-playType="5" data-max="11,">
              <!-- 十一选五 -->
              <div class="lnk-group">
                <a href="/syxw" class="btn-sup">自助选号</a>
                <a rel="nofollow" href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-11ydj-11.html" class="btn-sup">基本走势</a>
                <a href="/kaijiang/syxw" class="btn-sup">开奖详情</a>
              </div>

              <div class="mod-bet">
                <div class="mod-bet-hd">
                  <span>第<?php echo $issues[SYXW]['cIssue']['seExpect']?>期</span><span></span>
                </div>
                <div class="mod-bet-bd szc">
                  <div class="mod-bet-l ball-group-b inputArea"></div>

                  <div class="mod-bet-r">
                    <a href="javascript:;" target="_self" class="change"><i class="icon-font">&#xe625;</i>换一换</a><a href="javascript:;" class="btn btn-main btn-bet" target="_self">立即预约</a>
                  </div>
                </div>
                <div class="mod-bet-ft">
                  <div class="multi-modifier-s">
                    <a href="javascript:;" target="_self" class="minus">-</a>
                    <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                    <a href="javascript:;" target="_self" class="plus" data-max="99">+</a>
                  </div>倍<span>共 <em>2</em> 元</span>
                </div>
              </div>
            </div>
            <div class="mod-tab-item sssq-jczq" data-lottery="jczq" data-count='1' data-hot='1' data-midindex='0'>
              <!-- 竞彩足球 -->
              <div class="mod-bet">
                <div class="mod-bet-hd"></div>
                <div class="mod-bet-bd jczq">
                  <div class="mod-bet-l"></div>
                  <div class="mod-bet-r">
                    <a href="javascript:;" target="_self" class="change" data-lottery="jczq" data-div="sssq-jczq"><i class="icon-font">&#xe625;</i>换一换</a><a href="javascript:;" target="_self" class="btn btn-main btn-bet">立即预约</a>
                  </div>
                </div>
                <div class="mod-bet-ft">
                  <div class="multi-modifier-s">
                    <a href="javascript:;" target="_self" class="minus">-</a>
                    <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                    <a href="javascript:;" target="_self" class="plus" data-max="100000">+</a>
                  </div>倍<span>共 <em>2</em> 元</span><span>预计奖金：<em>233678.00~2124545</em>元</span>
                </div>
              </div>
            </div>
            <!-- 双色球end 大乐透-->
          </div>
        </div>
      </div>
      <!-- 试试手气 end -->
    </div>
    <div class="os-mod">
      <!-- 开奖公告 start -->
      <div class="os-mod-l">
        <div class="os-mod lottery-notice">
          <div class="os-mod-hd">
            <h4 class="os-mod-title"><i class='icon-font'>&#xe626;</i><a href="/kaijiang">开奖公告</a></h4>
          </div>
          <div class="os-mod-bd">
            <ul class="mod-tab">
              <li class="current">福彩</li>
              <li>体彩</li>
              <li>足彩</li>
            </ul>
            <div class="mod-tab-con">
              <div class="mod-tab-item" style="display: block;">
                <ul>
                  <li class="lottery-notice-info">
                    <h4><a href="/ssq">双色球</a><small><?php echo $issues[SSQ]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[SSQ]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php $awardArr = explode(':', $issues[SSQ]['lIssue']['awardNumber']);
                    foreach (explode(',', $awardArr[0]) as $award) {?>
                    	<span><?php echo $award?></span>
                    <?php }
                    foreach (explode(',', $awardArr[1]) as $award) {?>
                    	<span class="num-blue"><?php echo $award?></span>
                    <?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/ssq">详情</a>
                      <s class="split-line">|</s>
                      <a rel="nofollow" href="https://zoushi.166cai.cn/cjwssq/view/ssqzonghe.html">走势</a>
                      <s class="split-line">|</s>
                      <a href="/ssq">投注</a>
                    </div>
                  </li>
                  <li class="lottery-notice-info">
                    <h4><a href="/fcsd">福彩3D</a><small><?php echo $issues[FCSD]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[FCSD]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php foreach (explode(',', $issues[FCSD]['lIssue']['awardNumber']) as $award) {?>
                    	<span><?php echo $award?></span>
                    <?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/fc3d">详情</a>
                      <s class="split-line">|</s>
                      <a rel="nofollow" href="https://zoushi.166cai.cn/cjw3d/view/3d_danxuan.html">走势</a>
                      <s class="split-line">|</s>
                      <a href="/fcsd">投注</a>
                    </div>
                  </li>
                  <li class="lottery-notice-info">
                    <h4><a href="/qlc">七乐彩</a><small><?php echo $issues[QLC]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[QLC]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php $awardArr = explode(':', $issues[QLC]['lIssue']['awardNumber']);
                    foreach (explode(',', $awardArr[0]) as $award) {?>
                    	<span><?php echo $award?></span>
                    <?php }
                    foreach (explode(',', $awardArr[1]) as $award) {?>
                    	<span class="num-blue"><?php echo $award?></span>
                    <?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/qlc">详情</a>
                      <s class="split-line">|</s>
                      <a rel="nofollow" href="https://zoushi.166cai.cn/cjwqlc/view/qlcjiben.html">走势</a>
                      <s class="split-line">|</s>
                      <a href="/qlc">投注</a>
                    </div>
                  </li>
                </ul>
                <div class="more-box">
                  <a href="/kaijiang" class="more">更多开奖信息<i>&raquo;</i></a>
                </div>
              </div>
              <div class="mod-tab-item">
                <ul>
                  <li class="lottery-notice-info">
                    <h4><a href="/dlt">大乐透</a><small><?php echo $issues[DLT]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[DLT]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php $awardArr = explode(':', $issues[DLT]['lIssue']['awardNumber']);
                    foreach (explode(',', $awardArr[0]) as $award) {?>
                    	<span><?php echo $award?></span>
                    <?php }
                    foreach (explode(',', $awardArr[1]) as $award) {?>
                    	<span class="num-blue"><?php echo $award?></span>
                    <?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/dlt">详情</a>
                      <s class="split-line">|</s>
                      <a rel="nofollow" href="https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html">走势</a>
                      <s class="split-line">|</s>
                      <a href="/dlt">投注</a>
                    </div>
                  </li>
                  <li class="lottery-notice-info">
                    <h4><a href="/pls">排列三</a><small><?php echo $issues[PLS]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[PLS]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php foreach (explode(',', $issues[PLS]['lIssue']['awardNumber']) as $award) {?>
                    	<span><?php echo $award?></span>
                    <?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/pl3">详情</a>
                      <s class="split-line">|</s>
                      <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html">走势</a>
                      <s class="split-line">|</s>
                      <a href="/pls">投注</a>
                    </div>
                  </li>
                  <li class="lottery-notice-info">
                    <h4><a href="plw">排列五</a><small><?php echo $issues[PLW]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[PLW]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php foreach (explode(',', $issues[PLW]['lIssue']['awardNumber']) as $award) {?>
                    	<span><?php echo $award?></span>
                  	<?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/pl5">详情</a>
                      <s class="split-line">|</s>
                      <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl5/view/pl5_zst.html">走势</a>
                      <s class="split-line">|</s>
                      <a href="/plw">投注</a>
                    </div>
                  </li>
                  <li class="lottery-notice-info">
                    <h4><a href="/qxc">七星彩</a><small><?php echo $issues[QXC]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[QXC]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php foreach (explode(',', $issues[QXC]['lIssue']['awardNumber']) as $award) {?>
                    	<span><?php echo $award?></span>
                  	<?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/qxc">详情</a>
                      <s class="split-line">|</s>
                      <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html">走势</a>
                      <s class="split-line">|</s>
                      <a href="/qxc">投注</a>
                    </div>
                  </li>
                </ul>
                <div class="more-box">
                  <a href="/kaijiang" class="more">更多开奖信息<i>&raquo;</i></a>
                </div>
              </div>
              <div class="mod-tab-item">
                <ul>
                  <li class="lottery-notice-info">
                    <h4><a href="/sfc">胜负彩</a><small><?php echo $issues[SFC]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[SFC]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php foreach (explode(',', $issues[SFC]['lIssue']['awardNumber']) as $award) {?>
                    	<span><?php echo $award?></span>
                  	<?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/sfc">详情</a>
                      <s class="split-line">|</s>
                      <a href="/sfc">投注</a>
                    </div>
                  </li>
                  <li class="lottery-notice-info">
                    <h4><a href="/rj">任选九</a><small><?php echo $issues[RJ]['lIssue']['seExpect']?>期</small><span class="time"><?php echo date('Y-m-d', $issues[RJ]['lIssue']['awardTime']/1000)?></span></h4>
                    <div class="num-group">
                    <?php foreach (explode(',', $issues[RJ]['lIssue']['awardNumber']) as $award) {?>
                    	<span><?php echo $award?></span>
                  	<?php }?>
                    </div>
                    <div class="lnk-group">      
                      <a href="/kaijiang/rj">详情</a>
                      <s class="split-line">|</s>
                      <a href="/rj">投注</a>
                    </div>
                  </li>
                </ul>
                <div class="more-box">
                  <a href="/kaijiang" class="more">更多开奖信息<i>&raquo;</i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- 开奖公告 end -->

      <!-- 彩票资讯 start -->
      <div class="os-mod-r news">
        <div class="os-mod-hd">
          <h4 class="os-mod-title">彩票资讯</h4>
          <span class="lnk-group">热门彩种资讯：<a href="/info/lists/2?cpage=1">双色球</a><a href="/info/lists/4?cpage=1">大乐透</a><a href="/info/lists/6?cpage=1">竞彩足球</a><a href="/info" class="more">更多<i>&raquo;</i></a></span>
        </div>
        <div class="os-mod-bd win-news-box">
          <div class="news-item">
            <div class="news-item-hd">
                <span class="type-tag">数字彩</span>
                <h3>
                <?php for ($i = 1; $i <= 2; $i++) {?>
                	<a href="<?php echo $jingtai['numrm'][$i]['url']?>"><?php echo $jingtai['numrm'][$i]['title']?></a>
                <?php }?>
                </h3>
            </div>
            <div class="news-item-bd">
              <ul>
              <?php for ($i = 1; $i <= 4; $i++) {?>
              	<li>
                  <span class="news-item-tag">[<?php echo $jingtai['numtype'][$i]['title']?>]</span>
                  <a <?php if ($jingtai['num'.$i][1]['redflag'] == 1){?>class="main-color-s"<?php }?> href="<?php echo $jingtai['num'.$i][1]['url']?>"><?php echo $jingtai['num'.$i][1]['title']?></a>
                  <s class="split-line">|</s>
                  <a <?php if ($jingtai['num'.$i][2]['redflag'] == 1){?>class="main-color-s"<?php }?> href="<?php echo $jingtai['num'.$i][2]['url']?>"><?php echo $jingtai['num'.$i][2]['title']?></a>
                  <s class="split-line">|</s>
                  <a <?php if ($jingtai['num'.$i][3]['redflag'] == 1){?>class="main-color-s"<?php }?> href="<?php echo $jingtai['num'.$i][3]['url']?>"><?php echo $jingtai['num'.$i][3]['title']?></a>
                </li>
              <?php }?>
              </ul>
            </div>
          </div>
          <div class="news-item">
            <div class="news-item-hd">
              <span class="type-tag">竞技彩</span>
              <h3>
                <?php for ($i = 1; $i <= 2; $i++) {?>
                	<a href="<?php echo $jingtai['jcrm'][$i]['url']?>"><?php echo $jingtai['jcrm'][$i]['title']?></a>
                <?php }?>
              </h3>
            </div>
            <div class="news-item-bd">
              <ul>
                <?php for ($i = 1; $i <= 4; $i++) {?>
              	<li>
                  <span class="news-item-tag">[<?php echo $jingtai['jctype'][$i]['title']?>]</span>
                  <a <?php if ($jingtai['jc'.$i][1]['redflag'] == 1){?>class="main-color-s"<?php }?> href="<?php echo $jingtai['jc'.$i][1]['url']?>"><?php echo $jingtai['jc'.$i][1]['title']?></a>
                  <s class="split-line">|</s>
                  <a <?php if ($jingtai['jc'.$i][2]['redflag'] == 1){?>class="main-color-s"<?php }?> href="<?php echo $jingtai['jc'.$i][2]['url']?>"><?php echo $jingtai['jc'.$i][2]['title']?></a>
                  <s class="split-line">|</s>
                  <a <?php if ($jingtai['jc'.$i][3]['redflag'] == 1){?>class="main-color-s"<?php }?> href="<?php echo $jingtai['jc'.$i][3]['url']?>"><?php echo $jingtai['jc'.$i][3]['title']?></a>
                </li>
              <?php }?>
              </ul>
            </div>
          </div>
          <div class="win-news">
            <div class="win-news-hd">
              <h3>中奖快讯</h3>
              <?php $margin = $winnews['count']['margin'] + 0;
              $b = floor($margin/10000000000);$m = floor(($margin - $b*10000000000)/1000000);$y = floor(($margin - $b*10000000000 - $m * 1000000)/100)?>
              <p>本站累计中奖<em><?php if ($b > 0) {?><b><?php echo $b?></b>亿<?php }?><b><?php if ($m > 0) { echo $m?></b>万<?php }?><b><?php echo $y?></b>元</em></p>
            </div>
            <div class="win-news-bd">
              <ul>
              <?php $lidArr = array(DLT => '大乐透', SSQ => '双色球', JCZQ => '竞足', JCLQ => '竞篮', SYXW => '11选5', JXSYXW => '新11选5', FCSD => '3D',
              		 PLS => '排列三', KS => '快3', JLKS => '快3', JXKS => '快3', PLW => '排列五', QXC => '七星彩', QLC => '七乐彩', SFC => '胜负彩', RJ => '任选九', HBSYXW => '11选5', KLPK => '快乐扑克',
              		CQSSC => '时时彩', GDSYXW => '乐11选5');
              foreach ($winnews['orderInfo'] as $val) {?>
              <li><?php echo "【".$lidArr[$val['lid']]."】".uname_cut($val['nick_name'], 2, 3)?><em><?php echo number_format( $val['margin']/100, 2 )?><s>元</s></em></li>
              <?php }?>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- 彩票资讯 end -->
    </div>


    <div class="os-mod">
      <div class="os-mod-l lnk-group">
        <div class="jc-group">
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-92/" class="yc"><span>英超</span></a>
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-34/" class="yj">意甲</a>
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-85/" class="xj">西甲</a>
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-39/" class="dj">德甲</a>
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-93/" class="fj">法甲</a>
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-58/" class="olb">欧罗巴</a>
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>zuqiu-74/" class="og">欧冠</a>
          <a rel="nofollow" href="<?php echo $this->config->item('api_info')?>lanqiu/785/" class="nba">NBA</a>
        </div>

        <div class="jc-lnk">
          <ul>
            <li><a rel="nofollow" href="<?php echo $this->config->item('api_bf')?>"><i class="icon-font">&#xe621;</i>比分直播</a><a href="<?php echo $this->config->item('api_odds')?>"><i class="icon-font">&#xe628;</i>赔率中心</a></li>
            <li><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>"><i class="icon-font">&#xe62e;</i>足彩数据</a><a href="<?php echo $this->config->item('api_info')?>lanqiu"><i class="icon-font">&#xe627;</i>篮彩数据</a></li>
          </ul>        
        </div>
      </div>

      <!-- 竞彩专区 start -->
      <div class="os-mod-r jc-area">
        <div class="os-mod-hd">
          <h4 class="os-mod-title">竞彩专区</h4>
          <span class="lnk-group">热门彩种：<a href="/sfc">胜负彩</a><a href="/rj">任选九</a><a href="/jczq/hh">竞彩足球</a><a href="/jclq/hh">竞彩篮球</a><a href="/hall" class="more">更多<i>&raquo;</i></a></span>
        </div>
        <div class="os-mod-bd">
          <div class="jc-area-inner">
            <ul class="jc-area-tab">
              <li class="current"><strong>暂无赛事</strong></li><li><strong>暂无赛事</strong></li><li><strong>暂无赛事</strong></li>
            </ul>
            <div class="jc-area-con">
              <div class="jc-area-item item-first" data-lottery='jczq' data-count='1' style="display: block;">
                <div class="mod-bet">
                  <div class="mod-bet-hd"></div><div class="mod-bet-bd jczq"><div class="mod-bet-l"></div></div>
                  <div class="mod-bet-ft">
                    <a href="javascript:;" class="btn btn-main btn-bet" target="_self">立即预约</a>
                    <div class="multi-modifier-s">
                      <a href="javascript:;" target="_self" class="minus">-</a>
                      <label><input class="multi number" type="text" value="1" autocomplete="off"></label><a href="javascript:;" target="_self" class="plus" data-max="100000">+</a>
                    </div>倍<span>共 <em>2</em> 元</span><span>预计奖金：<em></em>元</span>
                  </div>
                </div>
              </div>
              <div class="jc-area-item item-second" data-lottery='jczq' data-count='1'>
                <div class="mod-bet">
                  <div class="mod-bet-hd"></div><div class="mod-bet-bd jczq"><div class="mod-bet-l"></div></div>
                  <div class="mod-bet-ft">
                    <a href="javascript:;" class="btn btn-main btn-bet" target="_self">立即预约</a>
                    <div class="multi-modifier-s">
                      <a href="javascript:;" target="_self" class="minus">-</a>
                      <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                      <a href="javascript:;" target="_self" class="plus" data-max="100000">+</a>
                    </div>倍<span>共 <em>2</em> 元</span><span>预计奖金：<em></em>元</span>
                  </div>
                </div>
              </div>
              <div class="jc-area-item item-third" data-lottery='jclq' data-count='1'>
              	<div class="mod-bet">
                  <div class="mod-bet-hd"></div><div class="mod-bet-bd jclq"><div class="mod-bet-l"></div></div>
                  <div class="mod-bet-ft">
                  	<a href="javascript:;" class="btn btn-main btn-bet" target="_self">立即预约</a>
                    <div class="multi-modifier-s">
                      <a href="javascript:;" target="_self" class="minus">-</a>
                      <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                      <a href="javascript:;" target="_self" class="plus" data-max="100000">+</a>
                    </div>倍<span>共 <em>2</em> 元</span><span>预计奖金：<em></em>元</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
      <!-- 竞彩专区 end -->
    </div>

  </div>
</div>
<?php $this->load->view('v1.1/elements/common/footer')?>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/slideFocus.js');?>'></script>
<script type="text/javascript">
var jczq = $.parseJSON('<?php echo $jczq?>'), jclq = $.parseJSON('<?php echo $jclq?>'), tm = 0, jzl = 0, jll = 0, jzmintime, jlmintime;
var hotjz = $.parseJSON('<?php echo $hotjz?>'), hotjl =$.parseJSON('<?php echo $hotjl?>'), hotz = 1, hotl = 1, midindexz = 0, midindexl = 0;
var ssqtime = 0, dlttime = 0, syxwtime = 0;
$(function(){
	if($.cookie('name_ie')) {
		$.get('/ajax/topBar', function(topBar){if(topBar) $('.top_bar').html(topBar);})
	}
})
</script>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/home.min.js');?>'></script>