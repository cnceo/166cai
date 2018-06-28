<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/index.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/buy.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/scroll.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/home.js');?>"></script>
<!--主内容区-->
<div class="indexWrap">
    <!--大图轮播-->
    <div class="bannerWrap">
        <ul class="fade_ul">
            <li class="fade_img" style="opacity: 1; z-index: 2;">
                <div style="background:url(<?php echo getStaticFile('/caipiaoimg/v1.0/images/carousel/carousel_1.png');?>) no-repeat center top; height:293px; width:100%;"></div>
                <span>
                    <a href="<?php echo $baseUrl; ?>jczq/hh" target="_blank"></a>
                </span>
            </li>
            <li class="fade_img">
                <div style="background:url(<?php echo getStaticFile('/caipiaoimg/v1.0/images/carousel/carousel_2.png');?>) no-repeat center top; height:293px; width:100%;"></div>
                <span>
                    <a href="<?php echo $baseUrl; ?>mobile" target="_blank"></a>
                </span>
            </li>
        </ul>
        <!--数字切换-->
        <div class="slide_no_box">
            <ul class="slider_num">
                <li>1</li>
                <li>2</li>
            </ul>
        </div>
     </div>

     <!--内容区-->
     <div class="index_wrap_center">
         <div class="menu_wrap">
            <div class="index_app_menu border">
                <ul>
                    <li><a id="czdh_ssq" href="<?php echo $baseUrl; ?>ssq" class="bannerSSQ"><strong>双色球</strong><span>2元可中1600万</span></a></li>
                    <li><a id="czdh_dlt" href="<?php echo $baseUrl; ?>dlt" class="bannerDLT"><strong>大乐透</strong><span>3元可中2400万</span></a></li>
                    <li><a id="czdh_jclq" href="<?php echo $baseUrl; ?>jclq/hh" class="bannerLC"><strong>竞彩篮球</strong><span>过关固定奖天天中</span></a></li>
                    <li><a id="czdh_jczq" href="<?php echo $baseUrl; ?>jczq/hh" class="bannerZC"><strong>竞彩足球</strong><span>买两场更易中奖</span></a></li>
                    <li><a id="czdh_syydj" href="<?php echo $baseUrl; ?>syxw" class="bannerKC"><strong>11运夺金</strong><span>10分钟开奖更好玩</span></a></li>
                </ul>
                <div class="nav_list">
                    <a href="<?php echo $baseUrl; ?>fcsd">福彩3D</a>
                    <a href="<?php echo $baseUrl; ?>qxc">七星彩</a>
                    <a href="<?php echo $baseUrl; ?>qlc">七乐彩</a>
                    <a href="<?php echo $baseUrl; ?>pls">排列三</a>
                    <a href="<?php echo $baseUrl; ?>plw">排列五</a>
                    <a href="javascript:void(0)" class="ban">北单</a>
                    <a href="<?php echo $baseUrl; ?>sfc">胜负彩</a>
                    <a href="<?php echo $baseUrl; ?>rj">任选9</a>
                    <a href="javascript:void(0)" class="ban">快3</a>
                </div>
             </div>
         </div>
         <!--右侧登录-->
         <div class="login_wrap">
            <div class="loginMessage">
                <h2>我的账户</h2>
                <?php if (empty($account)): ?>
                <div class="loginBefore">
                <p class="bwLogin"><a href="<?php echo $baseUrl; ?>passport">登录</a></p>
                <p class="bwRegister"><a href="<?php echo $baseUrl; ?>passport?target=1">注册</a></p>
                </div>
                <?php else: ?>
                <div class="loginAfter">
                    <strong class="my-money">
                        <a style="color: #fff;" href="<?php echo $baseUrl; ?>orders"><?php echo $this->money->format($account['amount'], 2); ?></a>
                    </strong>
                </div>
                <?php endif; ?>
                <p style="*margin-top:25px;">
                    <a href="<?php echo $baseUrl; ?>account/withdraw" style="background:none">提款</a>
                    <a href="<?php echo $baseUrl; ?>account/recharge">充值</a>
                </p>
            </div>
            <div class="login2WM">
                <a href="<?php echo $baseUrl; ?>mobile"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/'.$channelName.'_banner2WM.gif');?>" a=090lt="二维码下载" width="225" height="139" /></a>
            </div>
         </div>
         <!--右侧登录end-->
         <!-- 专题、推荐、新闻start-->
         <div class="activity border">
            <ul>
                <li><span>彩票圈</span></li>
                <li class="txt_right"><a href="<?php echo $baseUrl; ?>news">更多&gt;&gt;</a></li>
            </ul>
            <div class="topic-list">
            </div>
         </div>
         <!-- 专题、推荐、新闻end-->
         <!-- 公告start -->
         <div class="cement border">
            <h3>公告<a href="<?php echo $baseUrl; ?>news/index/<?php echo Cms_Model::CATE_NOTICE; ?>">更多&gt;&gt;</a></h3>
             <div class="notice-list">
             </div>
         </div>
         <!-- 公告end -->
     </div>
     <!--内容区end-->
</div>
<!--主内容区end-->

<!--彩票内容区-->
<div class="lotteryWrap clearfix">
    <!--左边-->
    <div class="lotteryLeft" style="margin-top: -5px;">
        <!--高赔率快投-->
        <div class="lotteryOne border">
            <div class="lotteryOneTit">
                <h2>高赔率快投</h2>
                <ul class="tabs">
                  <li class="selected" data-type="ssq"><em></em><span>双色球</span></li>
                  <li data-type="dlt"><em></em><span>大乐透</span></li>
                </ul>
                <p class="lottery-helper"><a href="<?php echo $baseUrl; ?>ssq">自助选号&gt;&gt;</a> <a href="<?php echo $baseUrl; ?>awards/number/<?php echo SSQ; ?>">开奖历史&gt;&gt;</a></p>
                <p class="lottery-helper" style="display: none;"><a href="<?php echo $baseUrl; ?>dlt">自助选号&gt;&gt;</a> <a href="<?php echo $baseUrl; ?>awards/number/<?php echo DLT; ?>">开奖历史&gt;&gt;</a></p>
            </div>
            <div class="lotteryOneInfo lottery-ssq">
                <div class="lotteryOneInfoA">
                    <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexSSQ.gif');?>" width="72" height="72" alt="双色球" />
                    <p>第<span class="curr-issue"></span>期</p>
                </div>
                <div class="lotteryOneInfoB">
                    <ul class="rand-ssq rand-nums">
                    </ul>
                    <p class="ssq-last-award">上期开奖号码： <span class="award-nums"></span>  奖金：1000万元</p>
                </div>
                <div class="lotteryOneInfoC">
                    <p class="bwIn"><a href="javascript:void(0)" class="switch-cast">换一注</a></p>
            		<?php if ($isLogin): ?>
          			<p class="bwBel"><a id="home_ssq_buy" href="javascript:void(0)" class="do-cast">立即投注</a></p>
          			<?php else: ?>
          			<p class="bwBel"><a id="home_ssq_buy" href="<?php echo $baseUrl; ?>passport">立即投注</a></p>
          			<?php endif; ?>
         			<div class="clear"></div>
                    <span class="count-down"></span>
                </div>
            </div>
            <div class="lotteryOneInfo lottery-dlt">
                <div class="lotteryOneInfoA">
                    <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexDLT.gif');?>" width="72" height="72" alt="大乐透" />
                    <p>第<span class="curr-issue"></span>期</p>
                </div>
                <div class="lotteryOneInfoB">
                    <ul class="rand-dlt rand-nums">
                    </ul>
                    <p class="dlt-last-award">上期开奖号码： <span class="award-nums"></span>  奖金：1000万元</p>
                </div>
                <div class="lotteryOneInfoC">
                    <p class="bwIn"><a href="javascript:void(0)" class="switch-cast">换一注</a></p>
            		<?php if ($isLogin): ?>
          			<p class="bwBel"><a id="home_dlt_buy" href="javascript:void(0)" class="do-cast">立即投注</a></p>
          			<?php else: ?>
          			<p class="bwBel"><a id="home_dlt_buy" href="<?php echo $baseUrl; ?>passport">立即投注</a></p>
          			<?php endif; ?>
         			<div class="clear"></div>
                    <span class="count-down"></span>
                </div>
            </div>
        </div>
        <!--高赔率快投end-->

        <!--高频彩快投-->
        <div class="lotteryOne border">
            <div class="lotteryOneTit">
                <h2>高频彩快投</h2>
                <ul>
                  <li class="selected"><em></em><span>老11选5</span></li>
                </ul>
                <p><a href="<?php echo $baseUrl; ?>syxw">自助选号&gt;&gt;</a> <a href="<?php echo $baseUrl; ?>awards/number/<?php echo SYXW; ?>">开奖历史&gt;&gt;</a></p>
            </div>
            <div class="lotteryOneInfo lottery-syxw">
                <div class="lotteryOneInfoA">
                    <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/index11.gif');?>" width="72" height="72" alt="11选5" />
                    <p>第<span class="curr-issue"></span>期</p>
                </div>
                <div class="lotteryOneInfoB2">
                    <ul class="rand-syxw rand-nums">
                    </ul>
                    <p class="syxw-last-award">上期开奖号码： <span class="award-nums"></span>  奖金：540元</p>
                </div>
                <div class="lotteryOneInfoC">
                    <p class="bwIn"><a href="javascript:void(0)" class="switch-cast">换一注</a></p>
            		<?php if ($isLogin): ?>
          			<p class="bwBel"><a id="home_syydj_buy" href="javascript:void(0)" class="do-cast">立即投注</a></p>
          			<?php else: ?>
          			<p class="bwBel"><a id="home_syydj_buy" href="<?php echo $baseUrl; ?>passport">立即投注</a></p>
          			<?php endif; ?>
         			<div class="clear"></div>
                    <span class="count-down"></span>
                </div>
            </div>
        </div>
        <!--高频彩快投end-->

        <!--竞技彩快投-->
        <div class="lotteryTwo border">
            <div class="lotteryOneTit">
                <h2>竞技彩快投</h2>
                <ul>
                  <li class="selected"><em></em><span>竞彩足球</span></li>
                </ul>
                <p><a href="<?php echo $baseUrl; ?>jczq">更多赛事&gt;&gt;</a> <a href="<?php echo $baseUrl; ?>awards/jczq">开奖历史&gt;&gt;</a></p>
            </div>
            <div class="lotteryTwoInfo lottery-jczq">
                <div class="lotteryTwoInfoA">
                    <p>
                        <img class="home-icon team-icon" src="<?php echo getStaticFile('/caipiaoimg/v1.0/file/team/football/default.png');?>" alt="主队" width="72" height="72" />
                        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/vs.gif');?>" alt="vs" width="40" height="23" />
                        <img class="away-icon team-icon" src="<?php echo getStaticFile('/caipiaoimg/v1.0/file/team/football/default.png');?>" alt="客队" width="72" height="72" /></p>
                    <p>
                        <strong class="home-name"></strong>
                        <span class="match-date"></span>
                        <strong class="away-name"></strong>
                    </p>
                </div>
                <div class="lotteryTwoInfoB">
                    <div class="fl">
                        <span class="guessSG selected switch-type">猜赛果</span>
                        <span class="guessBF switch-type">猜比分</span>
                    </div>
                    <div class="fr">
                        <div class="guessSGwrap play-panel">
                            <ul>
                              <li class="selected spf-option">胜 <span class="win-odd"></span></li>
                              <li class="spf-option">平 <span class="draw-odd"></span></li>
                              <li class="spf-option">负 <span class="lose-odd"></span></li>
                            </ul>
                            <p class="clearfix">
                                <strong>下注：</strong>
                                <input class="bet-money" type="text" value="10" />
                                <a href="javascript:void(0)" id="home_jczq_spf_buy" class="add-ten">+10元</a>
                            </p>
                            <h5 style="padding-left: 20px;">预测奖金：<span class="predict-money">0</span>元</h5>
                        </div>
                        <div class="guessBFwrap play-panel" style="display:none">
                            <p>
                                <select class="home-score">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <strong>：</strong>
                                <select class="away-score">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <strong>&nbsp;&nbsp;赔率：<span class="bf-odd"></span></strong>
                            </p>
                            <p>
                                <strong>下注：</strong>
                                <input class="bet-money" type="text" val="10" />
                                <a href="javascript:void(0)" id="home_jczq_cbf_buy" class="add-ten">+10元</a>
                            </p>
                            <h5>预测奖金：<span class="predict-money">0</span>元</h5>
                        </div>
                    </div>
                </div>
                <div class="lotteryTwoInfoC">
                    <?php if ($isLogin): ?>
          			<p class="bwBel"><a href="javascript:void(0)" class="cast-dgp">立即投注</a></p>
          			<?php else: ?>
          			<p class="bwBel"><a href="<?php echo $baseUrl; ?>passport">立即投注</a></p>
          			<?php endif; ?>
         			<div class="clear"></div>
                    <span class="count-down"></span>
                </div>
            </div>
            <div class="lotteryTwoLink">
                <ul class="candidate-matches">
                </ul>
            </div>
        </div>
        <!--竞技彩快投end-->

    </div>
    <!--右边-->
    <div class="lotteryRight" style="margin-top: -5px;">
        <!--公告-->
        <!--
        <div class="lotteryAtten">
            <h2>公告 <a href="#">更多&gt;&gt;</a></h2>
            <ul>
              <li><a href="#">[公告]世界杯期间竞彩足球限号</a></li>
              <li><a href="#">[公告]关于竞彩足球比赛推迟的</a></li>
              <li><a href="#">[公告]关于足球单场停止加奖4%</a></li>
              <li><a href="#">[公告]世界杯活动常见问题解</a></li>
            </ul>
        </div>
        -->
        <!--中奖排行榜-->
        <div class="lotteryList border">
            <h2>中奖排行榜</h2>
            <dl class="prize-list">
                <dt><em>排名</em><strong>用户</strong><span>中奖金额</span></dt>
            </dl>
        </div>
    </div>
    <!--右边end-->
</div>
<!--彩票内容区end-->

<!--合买彩票-->
<div class="lotteryJion border">
    <div class="lotteryJionTit">
        <h2>合买彩票</h2>
        <a href="<?php echo $baseUrl; ?>crowd">更多合买&gt;&gt;</a>
    </div>
    <div class="lotteryJionInfo">
        <span class="lotteryPre"></span>
        <span class="lotteryNext"></span>
        <div class="lotteryJionSlide">
            <ul>
            <?php foreach ($crowds as $crowd): ?>
              <li>
                <?php //$this->load->view('elements/crowd/home_item', array('item' => $crowd)); ?>
              </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
