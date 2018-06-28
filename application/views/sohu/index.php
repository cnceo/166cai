<link href="css/dialog.css?v=<?php echo VER_DIALOG_CSS; ?>" rel="stylesheet" type="text/css" />
<link href="css/sohu.css?v=<?php echo VER_SOHU_CSS; ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/scroll.js?v=<?php echo VER_SCROLL_JS; ?>"></script>
<script type="text/javascript" src="js/math.js?v=<?php echo VER_MATH_JS; ?>"></script>
<script type="text/javascript" src="js/lottery.js?v=<?php echo VER_LOTTERY_JS; ?>"></script>
<script type="text/javascript" src="js/home.js?v=<?php echo VER_HOME_JS; ?>"></script>
<script type="text/javascript">
$(function() {
    $('.do-cast').click(function() {
        _hmt.push(['_trackEvent', 'sohulanding', 'placeorder']);
    });
    $('body').on('click', '.btn-cancel', function() {
        _hmt.push(['_trackEvent', 'sohulanding', 'cancel']);
    });
    $('body').on('click', '.btn-confirm', function() {
        _hmt.push(['_trackEvent', 'sohulanding', 'confirm']);
    });
});
</script>
<!--主内容区-->
<div class="indexWrap">
  <!--top-->
  <div class="lotterySohuWrap">
    <div class="lotteryWrap clearfix">
      <p class="welcome">欢迎搜狐用户<span><?php echo $username; ?></span>!</p>
    </div>
  </div>
  <!--top end-->
  <!--bottom-->
  <div class="lotteryBottom">
  <div class="lotteryTit">
    <!--投注-->
    <div class="lotteryOne ">
      <div class="lotteryOneTit">
        <ul class="tabs">
          <li class="selected" data-type="dlt"><em>◆</em><span>大乐透</span></li>
          <li data-type="ssq"><em>◆</em><span>双色球</span></li>
        </ul>
        <p class="lottery-helper"><a href="<?php echo $baseUrl; ?>ssq">自助选号&gt;&gt;</a></p>
        <p class="lottery-helper" style="display: none;"><a href="<?php echo $baseUrl; ?>dlt">自助选号&gt;&gt;</a></p>
      </div>
      <div class="lotteryOneInfo lottery-ssq">
        <div class="lotteryOneInfoA"> <img src="images/bg/indexSSQ.gif" width="72" height="72" alt="双色球" />
          <p>第<span class="curr-issue"></span>期</p>
        </div>
        <div class="lotteryOneInfoB">
          <ul class="rand-ssq rand-nums">
          </ul>
          <p class="ssq-last-award">上期开奖号码： <span class="award-nums"></span> 奖金：1000万元</p>
        </div>
        <div class="lotteryOneInfoC">
          <p class="bwIn"><a href="javascript:void(0)" class="switch-cast">换一注</a>
          <p>
            <?php if ($isLogin): ?>
          <p class="bwBel"><a href="javascript:void(0)" class="do-cast">立即投注</a></p>
          <?php else: ?>
          <p class="bwBel"><a href="<?php echo $baseUrl; ?>passport">立即投注</a></p>
          <?php endif; ?>
          <div class="clear"></div>
          <span class="count-down"></span> </div>
      </div>
      <div class="lotteryOneInfo lottery-dlt">
        <div class="lotteryOneInfoA"> <img src="images/bg/indexDLT.gif" width="72" height="72" alt="大乐透" />
          <p>第<span class="curr-issue"></span>期</p>
        </div>
        <div class="lotteryOneInfoB">
          <ul class="rand-dlt rand-nums">
          </ul>
          <p class="dlt-last-award">上期开奖号码： <span class="award-nums"></span> 奖金：1000万元</p>
        </div>
        <div class="lotteryOneInfoC">
          <p class="bwIn"><a href="javascript:void(0)" class="switch-cast">换一注</a>
          <p>
            <?php if ($isLogin): ?>
          <p class="bwBel"><a href="javascript:void(0)" class="do-cast">立即投注</a></p>
          <?php else: ?>
          <p class="bwBel"><a href="<?php echo $baseUrl; ?>passport">立即投注</a></p>
          <?php endif; ?>
          <div class="clear"></div>
          <span class="count-down"></span> </div>
      </div>
    </div>
    <!--投注end-->
    <!--二维码下载start-->
    <div class="download">
      <dl>
        <dt><img style="width: 114px;" src="images/sohu/<?php echo $channelName; ?>_download.png" /></dt>
        <dd>扫一扫<br />
          中奖随时查</dd>
      </dl>
    </div>
    <!--二维码end-->
    <span class="clear"></span> </div>
    </div>
    <!--bottom end-->
</div>
<!--主内容区end-->
