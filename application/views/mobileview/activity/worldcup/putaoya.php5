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
<body ontouchstart="">
  <div class="wrap">
    <nav class="tab-list js-tab">
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/deguo" class="tab">德国</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/baxi" class="tab">巴西</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/putaoya" class="tab cur">葡萄牙</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/agenting" class="tab">阿根廷</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/bilishi" class="tab">比利时</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/bolan" class="tab">波兰</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/faguo" class="tab">法国</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/xibanya" class="tab">西班牙</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/bilu" class="tab">秘鲁</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/ruishi" class="tab">瑞士</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/yinggelan" class="tab">英格兰</a>
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
          <div class="team-pic"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/flag_putaoya.png');?>" alt=""></div>
          <p class="p-txt">伊比利亚半岛双雄之一的葡萄牙队，2016年欧洲杯，在小组赛险些被淘汰的情况下，由队长C罗吹响逆袭号角，淘汰赛稳扎稳打，最终问鼎欧洲，新贵葡萄牙能否趁热打铁，勇夺大力神杯！</p>
        </div>
        <!-- 头号球星-->
        <div class="g-square">
          <span class="tag">头牌球星</span>
          <div class="first-star">
            <h2 class="name">C罗</h2>
            <p class="p-info">过去十年，C罗一直保持着足以颠覆人类想象的现象级状态，大力神杯是C罗职业生涯的终极目标，也是这位7号传奇荣誉收藏室里缺少的唯一大件。更重要的是，这或许是他的最后一届世界杯，让我们且看且珍惜。</p>
            <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_cluo302.png');?>" alt="">
          </div>
        </div>
        <div class="g-square">
          <span class="tag red">经典时刻</span>
          <p class="p-txt">2016年的欧洲杯决赛，占据优势的法国90分钟内迟迟未能打破比赛僵局，加时赛第19分钟，葡萄牙替补前锋埃德尔禁区外突施冷箭，一记劲射攻破了法国人的球门。最终葡萄牙1-0击败法国。称雄欧陆的同时，也夺得了队史上的第一座国际大赛锦标。</p>
          <p class="p-txt">1966年世界杯，朝鲜击败意大利小组出线，成为一大黑马。八强战：开赛25分钟朝鲜便三球领先葡萄牙，令人瞠目结舌。接下来，堪称世界杯史上最伟大的逆转诞生了。葡萄牙核心尤西比奥上半场连扳两球，易边再战10分钟，“黑豹”右脚怒射扳平比分，3分钟后，尤西比奥点射完成大四喜，并帮助葡萄牙最终“让三追五”完成大翻盘。</p>
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
