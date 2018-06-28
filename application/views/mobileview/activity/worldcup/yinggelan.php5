<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="author" content="Yintao">
  <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui">
  <meta name="format-detection" content="telephone=no, email=no">
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
  <meta name="apple-mobile-web-app-title" content="166彩票">
  <title>世界杯球队巡礼</title>
  <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/world-cup-news.min.css');?>">
</head>
<body ontouchstart="" class="theme-blue">
  <div class="wrap">
    <nav class="tab-list js-tab">
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/deguo" class="tab">德国</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/baxi" class="tab">巴西</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/putaoya" class="tab">葡萄牙</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/agenting" class="tab">阿根廷</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/bilishi" class="tab">比利时</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/bolan" class="tab">波兰</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/faguo" class="tab">法国</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/xibanya" class="tab">西班牙</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/bilu" class="tab">秘鲁</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/ruishi" class="tab">瑞士</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/yinggelan" class="tab cur">英格兰</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/gelunbiya" class="tab">哥伦比亚</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/moxige" class="tab">墨西哥</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/wulagui" class="tab">乌拉圭</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/keluodiya" class="tab">克罗地亚</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/danmai" class="tab">丹麦</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/other" class="tab">其它</a>
    </nav>
      <div class="main">
        <!-- 球队信息 -->
        <div class="g-team-head">
          <div class="team-pic"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/flag_yinggelan.png');?>" alt=""></div>
          <p class="p-txt">作为现代足球的发源地，英格兰共参加过14次世界杯，并在1966年本土主办的世界杯上成功捧杯，获得过1次世界杯四强，6次八强，2次十六强。近年的表现被人们戏称为“欧洲中国队”，此番他们能否赢回三狮军团曾经的荣耀呢?</p>
        </div>

        <div class="g-square">
          <span class="tag">主要阵容</span>
          <p class="p-txt">哈里-凯恩、拉什福德、瓦尔迪、维尔贝克、斯特林、阿里、亨德森、斯通斯、卡希尔、沃克、特里皮尔、罗斯……</p>
        </div>

        <div class="g-square">
          <span class="tag red">趣闻</span>
          <p class="p-txt">1986年墨西哥世界杯上，四分之一决赛上，遭遇马拉多纳上帝之手的英格兰队遗憾1：2负于阿根廷队，这个误判，堪称世界杯历史上的一个污点。本届世界杯将引入视频助理裁判（VAR）以杜绝“冤假错案”的发生。</p>
          <p class="p-txt">1998年世界杯，1/8决赛中贝克汉姆受到西蒙尼的挑衅之后被红牌罚下，坎贝尔的致胜球遭到误判无效，被阿根廷2-2逼平进入点球大战而失利，英格兰止步十六强。</p>
          <p class="p-txt">2010年南非世界杯，1/8决赛，在落后德国队1球的情况下，兰帕德一记凌空垫射击中横梁，随后皮球反弹落入门线内，然而这个明显的进球被裁判判为无效，导致英格兰队止步16强。</p>
        </div>

      </div>
      <a class="g-btn" href="javascript:;">立即投注</a>
  </div>

  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>"></script>
  <script>
  $(function () {
      var menu = $('.tab-list');
      var menuItem = menu.find('.tab');
      var index = $('.cur').index();
      var rect = menuItem[index].getBoundingClientRect();
      var ww = document.documentElement.getBoundingClientRect().width;

      if (rect.right + 20 > ww) {
          menu.scrollLeft(rect.left - (ww - rect.width)/2);
      } else if (rect.left < 20) {
          $('.tab-list').scrollLeft((ww - rect.width)/2);
      }
  })
      $('.wrap').on('click', '.g-btn', function(){
        <?php if ($agent == 'app') {?>
        bet.btnclick('42', 'jczq');
        <?php } else {?>
        window.webkit.messageHandlers.doBet.postMessage({lid:'42'});
        <?php }?>
    })
  </script>
  <script>
    !function(e,t,n,g,i){e[i]=e[i]||function(){(e[i].q=e[i].q||[]).push(arguments)},n=t.createElement("script"),tag=t.getElementsByTagName("script")[0],n.async=1,n.src=('https:'==document.location.protocol?'https://':'http://')+g,tag.parentNode.insertBefore(n,tag)}(window,document,"script","assets.growingio.com/2.1/gio.js","gio");
    gio('init','8d4b2106242d6858', {});
    //custom page code begin here
    //custom page code end here
    gio('send');
  </script>
</body>
</html>
