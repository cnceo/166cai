<?php if (!$this->is_ajax): ?>
    <!DOCTYPE HTML>
    <html>
        <head>
            <meta charset="utf-8">
            <?php
            /* --- seo优化 --- @Author liusijia --- start --- */
            $this->config->load('seo');
            $seo = $this->config->item('seo');
            $set_data = $seo[$this->con][$this->act];
            $title = str_replace(array('#cnName#', '*date*', '#pageNumber#','#pageTitle#','#issue#', '#param0#', '#param1#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (($pageNumber > 1) ? '-第' . $pageNumber . '页' : ''),(!empty($pageTitle)?$pageTitle:''),(!empty($issue)?$issue:''), $param0, $param1), $set_data['title']);
            $keywords = ($pageNumber > 1) ? '' : str_replace(array('#cnName#', '*date*', '#issue#', '#param0#', '#param1#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (!empty($issue)?$issue:''), $param0, $param1), $set_data['keywords']);
            $description = ($pageNumber > 1) ? '' : str_replace(array('#cnName#', '*date*', '#issue#', '#param0#', '#param1#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (!empty($issue)?$issue:''), $param0, $param1), $set_data['description']);
            /* --- seo优化 --- @Author liusijia --- end --- */
            ?>
            <title><?php echo $title?$title:$s_title; ?></title>
            <meta content="<?php echo $description; //@Author liusijia   ?>" name="Description" />
            <meta content="<?php echo $keywords; //@Author liusijia  ?>" name="Keywords" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <meta name="renderer" content="webkit">
            <meta name="baidu-site-verification" content="lQnvYyQA6s" />
            <base href="<?php echo $baseUrl; ?>" <?php if (in_array($this->con, array('notice', 'help', 'hall'))): ?> target="_blank" <?php endif;?>>
            <!--[if lte IE 6]></base><![endif]-->
            <link rel="shortcut icon" href="/favicon.ico"/>
            <link rel="bookmark" href="/favicon.ico"/>
            <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>"/>
            <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
            <?php if (!$htype): ?>
            <link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-public.min.css');?>" rel="stylesheet" />
            <?php endif; ?>
        </head>
        <body>
            <script type="text/javascript">
                var baseUrl = '<?php echo $baseUrl; ?>';
                var uri = '<?php echo str_replace(array('<', '>', 'script'), '', $_SERVER['REQUEST_URI']);?>';
                var version = 'v1.2';
                var G = {
                	baseUrl: baseUrl
                }
                var visitor = {userNickname:'<?php echo empty($this->uid) ? '未登录用户' : $this->uinfo['uname']?>'};
				window.easemobim = window.easemobim || {};
				easemobim.config = {visitor: visitor};
			</script>
			<script src='//kefu.easemob.com/webim/easemob.js'></script>
 		<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.js'); ?>" type="text/javascript" ></script>
            <!--top begin-->
            <?php if (empty($this->uid)): 
            	$this->load->view('v1.1/elements/common/header_topbar_notlogin'); 
            else: 
            	$this->load->view('v1.1/elements/common/header_topbar'); 
            endif; ?>
            <!--top end-->

            <!--header begin-->
            <div class="header">
			  <div class="wrap header-inner">
			    <div class="logo">
			    	<div class="logo-txt"><span class="logo-txt-name">166彩票</span><span class="logo-txt-slogan">为生活添彩</span> 			</div>
			    	<a href="" target="_self" class="logo-img"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166.png');?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166@2x.svg');?> 2x" width="280" height="70" alt="166彩票网"></a>
			    	<a href="/activity/welcometo166" target="_blank" class="logo-active"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-active.png');?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-active@2x.png');?> 2x" width="194" height="70" alt="你预约，我护航"></a>
			    </div>
			    <div class="aside">
			    	<a href="javascript:;" onclick="easemobim.bind({tenantId: '38338'})" target="_self" class="btn online-service"><i class="icon-font">&#xe634;</i>在线客服</a>
			    	<p class="telphone"><i class="icon-font">&#xe633;</i>400-690-6760</p>
			    </div>
			  </div>
			</div>
            <!--header end-->
            <!--nav begin-->
            <div class="nav">
			  <div class="wrap_in">
			    <div class="nav-ticket">全部彩种<i class="icon-font icon-arrow">&#xe62a;</i>
			      <div class="lottery-categorys">
			        <ul class="lottery-categorys-a">
			          <li>
			            <a href="<?php echo $baseUrl; ?>ssq" class="item-a nav-ssq">
			             <div class="lottery-img"><svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg></div>
			              <p class="cont"><span class="title"><strong>双色球</strong><s>2元可中1000万</s></span></p>
			            </a>
			          </li>
			          <li>
			            <a href="<?php echo $baseUrl; ?>dlt" class="item-a nav-dlt">
			              <div class="lottery-img"><svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg></div>
			              <p class="cont"><span class="title"><strong>大乐透</strong></span></p>
			            </a>
			            <a href="/dlt" class="arrow-tag nav-dlt-tag"><?php if($dltPool){ echo $dltPool. '亿奖池';}else{ echo '3元可中1600万';}?></a>
			          </li>
			          <li>
			            <a href="<?php echo $baseUrl; ?>jczq/hh" class="item-a nav-jczq">
			              <div class="lottery-img"><svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg></div>
			              <p class="cont"><span class="title"><strong>竞彩足球</strong></span></p></a>
			              <a href="jczq/dg" class="arrow-tag nav-jclq-tag">单关固赔</a>
			          </li>
			          <li>
			            <a href="<?php echo $baseUrl; ?>jclq/hh" class="item-a nav-jclq">
			              <div class="lottery-img"><svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg></div>
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
			              <div class="lottery-img"><svg width="120" height="120"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="120" height="120"></image></svg></div>
			              <p class="cont"><span class="title"><strong>经典快3</strong></span></p>
			            </a>
			            <a href="<?php echo $baseUrl; ?>ks" class="arrow-tag nav-jclq-tag">摇骰子赢大奖</a>
			          </li>
			        </ul>
			        <ul class="lottery-categorys-b">
			          <li class="other"><p>数字彩</p><div class="tlist-m"><a href="<?php echo $baseUrl; ?>fcsd">福彩3D</a><a href="<?php echo $baseUrl; ?>qlc">七乐彩</a><a href="<?php echo $baseUrl; ?>pls">排列三</a><a href="<?php echo $baseUrl; ?>qxc">七星彩</a><a href="<?php echo $baseUrl; ?>plw">排列五</a></div></li>
			          <li class="other has-hideitem">
			              <p>高频彩</p>
			              <div class="tlist-m tlist-m-l">
			                  <a href="<?php echo $baseUrl; ?>hbsyxw" style="margin-right: 0px;padding-right: 0px;">惊喜11选5</a>
			                  <a href="<?php echo $baseUrl; ?>syxw">老11选5<span class="arrow-tag nav-syxw-tag">热</span></a>
			                  <a href="<?php echo $baseUrl; ?>jxks" class="main-color-s">红快3</a>
			                  <a href="<?php echo $baseUrl; ?>jlks" class="main-color-s">易快3<span class="arrow-tag nav-syxw-tag">加奖</span></a>
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
			      <li class="<?php echo ($this->con == 'hemai' || $this->con == 'gendan') && in_array($this->act, array('index', 'ssq', 'dlt', 'pls', 'fcsd', 'qlc', 'qxc', 'jczq', 'jclq', 'sfc')) ? 'cur' : ''; ?>"><a target="_blank" href="/hemai">合买<i class="icon-font icon-arrow">&#xe62a;</i></a>
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
			        <a href="/activity/newmode" target="_blank"><i class="icon-font">&#xe61c;</i>手机彩票<div class="qrcode"><h2>免费下载手机客户端</h2><p>彩店出票 领奖无忧</p><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/qrcode.png');?>" width="94" height="94" alt=""></div></a>
			      </li>
			    </ul>
			  </div>
			</div>
            <!--nav end-->
        <?php endif; ?>
