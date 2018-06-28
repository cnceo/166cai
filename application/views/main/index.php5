<base target="_blank" />
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/index.css'); ?>"/>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/scroll.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/home.js'); ?>"></script>
<script type="text/javascript">
    $(function() {
        cx.closeCount = true; 
        var ssqAward = '<?php echo $awards[Lottery_Model::SSQ]['awardNumber']; ?>';
        var ssqHtml = cx.Lottery.renderAward(cx.Lottery.SSQ, ssqAward);
        $('.ssq-award-nums').html(ssqHtml);

        var syxwAward = '<?php echo $awards[Lottery_Model::SYYDJ]['awardNumber']; ?>';
        var syxwHtml = cx.Lottery.renderAward(cx.Lottery.SYXW, syxwAward);
        $('.syxw-award-nums').html(syxwHtml);

        var dltAward = '<?php echo $awards[Lottery_Model::DLT]['awardNumber']; ?>';
        var dltHtml = cx.Lottery.renderAward(cx.Lottery.DLT, dltAward);
        $('.dlt-award-nums').html(dltHtml);

        var fcsdAward = '<?php echo $awards[Lottery_Model::FCSD]['awardNumber']; ?>';
        var fcsdHtml = cx.Lottery.renderAward(cx.Lottery.FCSD, fcsdAward);
        $('.fcsd-award-nums').html(fcsdHtml);

        var qxcAward = '<?php echo $awards[Lottery_Model::QXC]['awardNumber']; ?>';
        var qxcHtml = cx.Lottery.renderAward(cx.Lottery.QXC, qxcAward);
        $('.qxc-award-nums').html(qxcHtml);

        var qlcAward = '<?php echo $awards[Lottery_Model::QLC]['awardNumber']; ?>';
        var qlcHtml = cx.Lottery.renderAward(cx.Lottery.QLC, qlcAward);
        $('.qlc-award-nums').html(qlcHtml);

        var plsAward = '<?php echo $awards[Lottery_Model::PLS]['awardNumber']; ?>';
        var plsHtml = cx.Lottery.renderAward(cx.Lottery.PLS, plsAward);
        $('.pls-award-nums').html(plsHtml);

        var plwAward = '<?php echo $awards[Lottery_Model::PLW]['awardNumber']; ?>';
        var plwHtml = cx.Lottery.renderAward(cx.Lottery.PLW, plwAward);
        $('.plw-award-nums').html(plwHtml);

        var rjAward = '<?php echo str_replace(',', ' ', $awards[Lottery_Model::RJ]['awardNumber']); ?>';
        var rjHtml = cx.Lottery.renderAward(cx.Lottery.RJ, rjAward);
        $('.rj-award-nums').html(rjHtml.replace(/ball-red/g, '').replace(/ball-blue/g, ''));

        var sfcAward = '<?php echo $awards[Lottery_Model::SFC]['awardNumber']; ?>';
        var sfcHtml = cx.Lottery.renderAward(cx.Lottery.SFC, sfcAward);
        $('.sfc-award-nums').html(sfcHtml.replace(/ball-red/g, '').replace(/ball-blue/g, ''));

    });
</script>
<div class="wrap_in index-container">
    <!-- 首屏 begin-->
    <div class="mod-main clearfix">
        <div class="lottery-categorys">
            <ul>
                <li>
                    <a href="<?php echo $baseUrl; ?>ssq" class="item-a nav-ssq">
                        <s>
                           <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="168" height="126" alt=""> 
                        </s>
                        <p class="cont">
                            <em class="title"><strong class="pause">双色球</strong><span class="grayWords"><i class="arrowsIcon"></i>停售</span></em>
                            <span class="word">2元最高可中1000万</span>
                        </p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>dlt" class="item-a nav-dlt">
                        <s>
                          <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="168" height="126" alt="">  
                        </s>
                        <p class="cont">
                            <em class="title"><strong class="pause">大乐透</strong><span class="grayWords"><i class="arrowsIcon"></i>停售</span></em>
                            <span class="word">2元最高可中1000万</span>
                        </p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>jclq/hh" class="item-a nav-jclq">
                        <s>
                          <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="168" height="126" alt="">  
                        </s>
                        <p class="cont">
                            <em class="title"><strong class="pause">竞彩篮球</strong><span class="grayWords"><i class="arrowsIcon"></i>停售</span></em>
                            <span class="word">69%返奖率，覆盖NBA比赛</span>
                        </p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>jczq/hh" class="item-a nav-jczq no-hover-bg">
                        <s>
                          <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="168" height="126" alt="">  
                        </s>
                        <p class="cont">
                            <em class="title"><strong class="pause">竞彩足球</strong><!-- <span class="redWords"><i class="arrowsIcon"></i>单关上线</span> --><span class="grayWords"><i class="arrowsIcon"></i>停售</span></em>
                            <span class="word">返奖率最高73%，足球迷首选</span>
                        </p>
                    </a>
                    <div class="expand tlist-m clearfix">
                        <a href="<?php echo $baseUrl; ?>jczq/hh" class="pause">混合过关</a>
                        <a href="<?php echo $baseUrl; ?>jczq/dg" class="pause">单关</a>
                        <a href="<?php echo $baseUrl; ?>jczq/rqspf" class="pause">让球胜平负</a>
                        <a href="<?php echo $baseUrl; ?>jczq/cbf" class="pause">比分</a>
                        <a href="<?php echo $baseUrl; ?>jczq/spf" class="pause">胜平负</a>
                        <a href="<?php echo $baseUrl; ?>jczq/jqs" class="pause">总进球</a>
                    </div>
                </li>
                <li class="other">
                    <p>数字彩</p>
                    <div class="tlist-m clearfix">
                        <a href="<?php echo $baseUrl; ?>fcsd" class="pause">福彩3D</a>
                        <a href="<?php echo $baseUrl; ?>qlc" class="pause">七乐彩</a>
                        <a href="<?php echo $baseUrl; ?>pls" class="pause">排列三</a>
                        <a href="<?php echo $baseUrl; ?>qxc" class="pause">七星彩</a>
                        <a href="<?php echo $baseUrl; ?>plw" class="pause">排列五</a>
                        <a href="<?php echo $baseUrl; ?>syxw" class="pause">老11选5</a>
                    </div>
                </li>
                <li class="other">
                    <p>竞技彩</p>
                    <div class="tlist-m clearfix">
                        <a href="<?php echo $baseUrl; ?>sfc" class="pause">胜负彩<span class="grayWords"><i class="arrowsIcon"></i>停售</span></a>
                        <a href="<?php echo $baseUrl; ?>rj" class="pause">任选九<span class="grayWords"><i class="arrowsIcon"></i>停售</span></a>
                        <!-- <em><a href="<?php echo $baseUrl; ?>jclq/hh">竞彩篮球</a></em> -->
                    </div>
                </li>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="mid-cont">
            <!-- slide -->
            <div class="slide-wrap" id="J_slideWrap">
                <ul class="slide">
                	<li style="display:block;left:0;"><a href="/activity/league" class='zhuanti11'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/500x170-9.jpg'); ?>" width="500" height="170" alt=""></a></li>
                	<li><a href="/notice/detail/2024"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/500x170-8.jpg'); ?>" alt=""></a></li>
                	<li><a href="/activity/jczq" class='zhuanti1'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/500x170-7.jpg'); ?>" alt=""></a></li>
                	<li><a href="javascript:;"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/kaishou.png'); ?>" width="500" height="170" alt=""></a></li>
                    <!--<li><a href="/activity/jczq"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/500x170-7.jpg'); ?>" alt=""></a></li>
                    <li><a href="/jclq"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/500x170-5.jpg'); ?>" width="500" height="170" alt=""></a></li>
                     -->
                </ul>
                <div class="slide-info opc5">
                    <a href="/activity/league" style="display:inline;" class='zhuanti11'>图说五大联赛，扫清投注障碍</a>
                	<a href="/notice/detail/2024">2345彩票委托投注业务暂停通知</a>
                	<a href="/activity/jczq" class='zhuanti1'>手把手带你入门竞彩足球</a>
                	<a>留下手机号，开售早知道！</a>
                    <!-- <a href="/activity/jczq">手把手带你入门竞彩足球</a>
                    <a href="/jclq">NBA赛事火热进行中</a> -->
                </div>
                <div class="slide-dot" id="slideDot"><span class="current"></span><span class=""></span><span class=""></span><span class=""></span></div>
            </div>
            <!-- slide end -->
            <!-- 新手教程 -->
            <div class="newbie">
                <h4 class="newbie-title">新手入门</h4>
                <ol class="newbie-steps clearfix">
                    <li><a title='注册登录' href="<?php echo $baseUrl; ?>help/index/b0-s1"><i class="n-ico1"></i>注册登录</a></li>
                    <li class="arrow"></li>
                    <li><a title='充值' href="<?php echo $baseUrl; ?>help/index/b1-s1-f1"><i class="n-ico2"></i>充值</a></li>
                    <li class="arrow"></li>
                    <li><a title='购买彩票' href="<?php echo $baseUrl; ?>help/index/b2"><i class="n-ico3"></i>购买彩票</a></li>
                    <li class="arrow"></li>
                    <li><a title='开奖中奖' href="<?php echo $baseUrl; ?>help/index/b3-s1"><i class="n-ico4"></i>开奖中奖</a></li>
                    <li class="arrow"></li>
                    <li><a title='兑奖提款' href="<?php echo $baseUrl; ?>help/index/b3-s2"><i class="n-ico5"></i>兑奖提款</a></li>
                </ol>
            </div>
            <!-- 新手教程 end -->
            <!-- 双色球快速投注 -->
            <div class="lot-fastbet">
                <div class="fastbet-menu clearfix">
                    <h4 class="lot-fastbet-title">试试手气</h4>
                    <ul class="tab-menu clearfix">
                        <li class="current"><span class="arrow"></span>双色球</li>
                        <li class=""><span class="arrow"></span>大乐透</li>
                        <li class=""><span class="arrow"></span>七乐彩</li>
                        <li class="last"><span class="arrow"></span>老11选5</li>
                    </ul>
                </div>
                <div class="rapid-bet">
                    <!-- 双色球 -->
                    <div class="lotteryBetArea rapid-bet-bd lottery-ssq lucky-ssq" style="display:block;">
                        <div class="rapid-bet-cont">    
                            <span class="cz-logo"><a target="_blank" href="/ssq"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="320" height="240" alt="" ></a></span>
                            <p class="periods">第<b><span class="curr-issue"></span></b>期&nbsp;&nbsp;&nbsp;&nbsp;投注截止时间：<span class="retime count-down"></span></p>
                            <div class="inputArea clearfix rand-ssq rand-nums">
                            </div>
                        </div>
                        <div class="btn-box">
                            <a href="javascript:;" target="_self" class="btn btn-bet-sp do-cast">立即投注</a>
                            <a href="javascript:;" target="_self" class="change switch-cast"><i></i>换一换</a>
                        </div>
                        <div class="intr-foot">2元赢取1000万，每周二、四、日开奖</div>
                    </div>
                    <!-- 大乐透 -->
                    <div class="lotteryBetArea rapid-bet-bd lottery-dlt lucky-dlt">
                        <div class="rapid-bet-cont">    
                            <span class="cz-logo"><a target="_blank" href="/dlt"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="320" height="240" alt="" ></a></span>
                            <p class="periods">第<b><span class="curr-issue"></span></b>期&nbsp;&nbsp;&nbsp;&nbsp;投注截止时间：<span class="retime count-down"></span></p>
                            <div class="inputArea clearfix rand-dlt rand-nums">
                            </div>
                        </div>
                        <div class="btn-box">
                            <a href="javascript:;" target="_self" class="btn btn-bet-sp do-cast">立即投注</a>
                            <a href="javascript:;" target="_self" class="change switch-cast"><i></i>换一换</a>
                        </div>
                        <div class="intr-foot">2元最高可中1000万</div>
                    </div>
                    <!-- 七乐彩 -->
                    <div class="lotteryBetArea rapid-bet-bd lottery-qlc lucky-qlc">
                        <div class="rapid-bet-cont">    
                            <span class="cz-logo"><a target="_blank" href="/qlc"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="320" height="240" alt="" ></a></span>
                            <p class="periods">第<b><span class="curr-issue"></span></b>期&nbsp;&nbsp;&nbsp;&nbsp;投注截止时间：<span class="retime count-down"></span></p>
                            <div class="inputArea clearfix rand-qlc rand-nums">
                            </div>
                        </div>
                        <div class="btn-box">
                            <a href="javascript:;" target="_self" class="btn btn-bet-sp do-cast">立即投注</a>
                            <a href="javascript:;" target="_self" class="change switch-cast"><i></i>换一换</a>
                        </div>
                        <div class="intr-foot">2元赢取500万，每周一、三、五开奖</div>
                    </div>
                    <!-- 老11选5 -->
                    <div class="lotteryBetArea rapid-bet-bd lottery-syxw lucky-syxw">
                        <div class="rapid-bet-cont">    
                            <span class="cz-logo"><a target="_blank" href="/syxw"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" width="320" height="240" alt="" ></a></span>
                            <p class="periods">第<b><span class="curr-issue"></span></b>期&nbsp;&nbsp;&nbsp;&nbsp;投注截止时间：<span class="retime count-down"></span></p>
                            <div class="inputArea clearfix rand-syxw rand-nums">
                            </div>
                        </div>
                        <div class="btn-box" style="white-space-collapse:discard;">
                            <a href="javascript:;" target="_self" class="btn btn-bet-sp do-cast">立即投注</a>
                            <a href="javascript:;" target="_self" class="change switch-cast"><i></i>换一换</a>
                        </div>
                        <div class="intr-foot">十分钟一期，2元赢取540元</div>
                    </div>
                    <!-- 双色球end 大乐透-->
                </div>
            </div>
            <!-- 双色球快速投注 end -->
        </div>
        <!-- 右侧模块 -->
        <div class="right-mod">
            <div class="fast-login">
                <?php $this->load->view('elements/common/fast_login'); ?>
            </div>
            <div class="show-mod">
                <p class="show-mod-txt">A股上市公司旗下网站<small>股票代码&nbsp;002195</small></p>
                <ul class="safe-shield clearfix">
                    <li>账户安全</li>
                    <li>投注安全</li>
                    <li>兑奖安全</li>
                    <li>提款安全</li>
                </ul>
                <p class="promise">我们承诺：<b>100%真实出票！</b></p>
                <dl class="total-volume">
                    <dt>本站累计中奖金额</dt>
                    <dd><em><?php echo implode('</em><em>', $total_win);?></em>万元</dd>
                </dl>
                <div class="notify">
                    <ul class="n-tab clearfix">
                        <li class="active"><a href="<?php echo $baseUrl;?>notice/index">网站公告</a></li>
                        <li class=""><a href="<?php echo $baseUrl;?>help/index/b4-i1">帮助中心</a></li>
                    </ul>
                    <div class="n-tabWrap">
                        <div class="n-cont" id="index_notice_help0">
                            <ul class="txtList-d">
                                <?php
                                //@Author liusijia
                                if ($noticeInfo) {
                                    foreach ($noticeInfo as $v) {
                                        ?>
                                        <li><i></i><a target="_blank" href="<?php echo $baseUrl; ?>notice/detail/<?php echo $v['id']; ?>"><?php echo $v['title']; ?></a></li>
                                    <?php }
                                } ?>
                            </ul>
                        </div>
                        <div class="n-cont" style="display:none;" id="index_notice_help1">
                            <ul class="txtList-d">
                                <li><i></i><a href="<?php echo $baseUrl; ?>help/index/b0-s1-f1">怎么注册2345彩票帐号？</a></li>
                                <li><i></i><a href="<?php echo $baseUrl; ?>help/index/b1-s1-f1">怎么给我的帐户充值？</a></li>
                                <li><i></i><a href="<?php echo $baseUrl; ?>help/index/b2-s1-f1">如何在2345购买彩票？</a></li>
                                <li><i></i><a href="<?php echo $baseUrl; ?>help/index/b3-s2-f1">中奖后如何兑奖？</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 右侧模块 end -->

    </div>
    <!-- 首屏 end-->
    <div class="mod-middle clearfix">
        <div class="mod-box lottery-notice">
            <div class="th_a mod-box-hd">
                <h3 class="mod-box-title">开奖公告</h3>
            </div>
            <div class="tb_a boxCon">
                <ul class="lotteryTypeList clearfix">
                    <li class="notice-ssq">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">双色球</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::SSQ]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::SSQ]['awardTime'] / 1000); ?></p>
                        <p class="pNum ssq-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/ssq" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>ssq">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-dlt">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">大乐透</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::DLT]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::DLT]['awardTime'] / 1000); ?></p>
                        <p class="pNum dlt-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/dlt" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>dlt">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-fcsd">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">福彩3D</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::FCSD]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::FCSD]['awardTime'] / 1000); ?></p>
                        <p class="pNum fcsd-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/fc3d" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>fcsd">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-qxc">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">七星彩</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::QXC]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::QXC]['awardTime'] / 1000); ?></p>
                        <p class="pNum qxc-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/qxc" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>qxc">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-qlc">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">七乐彩</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::QLC]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::QLC]['awardTime'] / 1000); ?></p>
                        <p class="pNum qlc-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/qlc" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>qlc">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-pls">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">排列三</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::PLS]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::PLS]['awardTime'] / 1000); ?></p>
                        <p class="pNum pls-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/pl3" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>pls">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-plw">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">排列五</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::PLW]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::PLW]['awardTime'] / 1000); ?></p>
                        <p class="pNum plw-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/pl5" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>plw">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-syxw">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">老11选5</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::SYYDJ]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::SYYDJ]['awardTime'] / 1000); ?></p>
                        <p class="pNum syxw-award-nums"></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/syxw" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>syxw">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-sfc">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">胜负彩</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::SFC]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::SFC]['awardTime'] / 1000); ?></p>
                        <p class="pNum sfc-award-nums award-nums award-nums-nobg allBlack"><?php echo str_replace(',', ' ', $awards[Lottery_Model::SFC]['awardNumber']); ?></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/sfc" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>sfc">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-rj">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">任选九</span>
                            <span class="sDes">第<?php echo $awards[Lottery_Model::RJ]['seExpect']; ?>期</span>
                        </div>
                        <p class="pTime">开奖时间：<?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::RJ]['awardTime'] / 1000); ?></p>
                        <p class="pNum rj-award-nums award-nums award-nums-nobg allBlack"><?php echo str_replace(',', ' ', $awards[Lottery_Model::RJ]['awardNumber']); ?></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>kaijiang/rj" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>rj">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-jczq">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">竞彩足球</span>
                        </div>
                        <p class="pSelectStyle">
                            <a href="<?php echo $baseUrl; ?>jczq/spf" target="_self">胜平负</a>
                            <a href="<?php echo $baseUrl; ?>jczq/rqspf" target="_self">让球胜平负</a>
                            <a href="<?php echo $baseUrl; ?>jczq/cbf" target="_self">比分</a>
                            <a href="<?php echo $baseUrl; ?>jczq/hh" target="_self">混合过关</a>
                            <a href="<?php echo $baseUrl; ?>jczq/jqs" target="_self">进球数</a>
                            <a href="<?php echo $baseUrl; ?>jczq/bqc" target="_self">半全场</a>
                        </p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>awards/jczq" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>jczq/hh">立即投注</a>
                        </p>
                    </li>
                    <li class="notice-jclq">
                        <div class="picTxt">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png'); ?>" alt="" title="" width="228" height="171" class="logoPic">
                            <span class="sName">竞彩篮球</span>
                        </div>
                        <p class="pSelectStyle">
                            <a href="<?php echo $baseUrl; ?>jclq/rfsf" target="_self">让分胜负</a>
                            <a href="<?php echo $baseUrl; ?>jclq/sf" target="_self">胜负</a>
                            <a href="<?php echo $baseUrl; ?>jclq/sfc" target="_self">胜分差</a>
                            <a href="<?php echo $baseUrl; ?>jclq/dxf" target="_self">大小分</a>
                            <a href="<?php echo $baseUrl; ?>jclq/hh" target="_self">混合过关</a></p>
                        <p class="pBtn">
                            <a href="<?php echo $baseUrl; ?>awards/jclq" target="_blank" class="oldLotteryBtn">开奖详情</a>
                            <a class="btn btn-bet-small" href="<?php echo $baseUrl; ?>jclq/hh">立即投注</a>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mod-box lottery-help">
            <div class="mod-box-hd">
                <h3 class="mod-box-title">购彩帮助</h3>
            </div>
            <div class="mod-box-bd">
                <ol class="helpList">
                    <li>
                        <h4>1.中奖后如何兑奖？</h4>
                        <p>小奖均直接派奖到帐户；大奖（单注奖金超过1万或订单奖金总额超过5万）请等待客服与您联系并确定领取方案，您可选择领取纸质票后自行领取，也可...<a href="<?php echo $baseUrl; ?>help/index/b3-s2-f1" target="_blank">详细</a></p>
                    </li>
                    <li>
                        <h4>2.充值遇到问题怎么办？</h4>
                        <p>如充值未到账，请您15分钟后再查看账户余额。若仍未到账，请核实是否成功扣款。若未成功扣款请您重新充 ...<a href="<?php echo $baseUrl; ?>help/index/b1-s1-f4" target="_blank">详细</a></p>
                    </li>
                    <li>
                        <h4>3.是否可以拿到纸质票？</h4>
                        <p>可以。中心派奖后，可联系客服申请纸质票邮寄服务，邮资自理（需您自领的大奖，请联系客服到公司所在地当面领取纸质票）</p>
                    </li>
                    <li>
                        <h4>4. 在2345彩票如何完成购彩？</h4>
                        <p>您提交彩票投注方案并成功付款后，系统将第一时间根据您的方案进行投注，您可在订单详情中查看订单处理进度。</p>
                    </li>
                    <li>
                        <h4>5.2345彩票如何保障购彩安全？</h4>
                        <ol>
                            <li>1）登录密码与交易密码双重保护</li>
                            <li>2）购彩实名制</li>
                            <li>3）大奖全程协助领取</li>
                        </ol>
                    </li>
                </ol>
            </div>
        </div>
    </div>
    <!--开售提醒-->
<div class="qrcode-app-fixed">
    <h3>扫码下载客户端</h3>
    <p>大奖就在你手中!</p>
    <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/qrcode-app.png');?>" width="108" height="108" alt="">
</div>
    <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/kaishou.js'); ?>"></script>
<script>
    /**
     * 网站公告 和 帮助中心 相互切换
     * @Author liusijia
     */
    function changeMethod(k){
        $('.n-cont').hide();
        $('#index_notice_help'+k).show();
        if(k == 0){
            $('#web_notice0').attr('class','active');
            $('#web_notice1').attr('class','');
        }else if(k == 1){           
            $('#web_notice0').attr('class','');
            $('#web_notice1').attr('class','active');
        }
    } 
</script>
<script type="text/javascript">
$(function() {
    //阅读数加1，第十一个专题对应第10个数据，order为10
	$('.zhuanti11').click (function(){
		$.ajax({
            type: 'POST',
            url: '/academy/countClick',
            data: {
                'order':10
            }
        });
	});

	 //阅读数加1，第一个专题对应第1个数据，order为0
	$('.zhuanti1').click (function(){
		$.ajax({
            type: 'POST',
            url: '/academy/countClick',
            data: {
                'order':0
            }
        });
	});


    //首页开奖提示输入框提示js
    function hasPlaceholderSupport() {
        var input = document.createElement('input');
        return ('placeholder' in input);
    }
    var tipBoxIpt = $('.form-item-ipt'),
        message = tipBoxIpt.attr('placeholder');
        if(hasPlaceholderSupport()){
            tipBoxIpt.each(function(){
              $(this).val('');
            });
        }else{
            tipBoxIpt.on({
                focus: function(){
                $(this).val('');
            },
                blur: function(){
                if($(this).val() == ''){
                  $(this).val($(this).attr('placeholder'));
                }
            }
        });
    }
})
</script>