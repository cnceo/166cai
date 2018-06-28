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
<body ontouchstart=""class="theme-yellow">
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
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/yinggelan" class="tab">英格兰</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/gelunbiya" class="tab">哥伦比亚</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/moxige" class="tab">墨西哥</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/wulagui" class="tab">乌拉圭</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/keluodiya" class="tab">克罗地亚</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/danmai" class="tab cur">丹麦</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/other" class="tab">其它</a>
    </nav>
      <div class="main">
        <!-- 球队信息 -->
        <div class="g-team-head">
          <div class="team-pic"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/flag_danmai.png');?>" alt=""></div>
          <p class="p-txt">有着“红色炸药”之称的丹麦，作为北欧足球的传统强队之一，历史上一共4次晋级世界杯决赛圈。球队荣誉首先当数92年欧洲杯夺冠上演足球届的“安徒生童话”和95年的联合会杯冠军。</p>
        </div>
        <!-- 主要球星 -->
        <div class="g-square">
          <span class="tag">主要球星</span>
          <div class="main-star">
            <ul class="star-list">
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_ailikesen72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">埃里克森</h3>
                  <p class="p-info">埃里克森作为丹麦和热刺的中场核心，拥有不俗的组织能力和得分能力，远射任意球也是顶级水准，他的发挥将会直接影响北欧劲旅丹麦队的表现。</p>
                </div>
              </li>
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_kelisitengsen72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">克里斯滕森</h3>
                  <p class="p-info">小将克里斯滕森本赛季在切尔西坐稳了主力中卫的位置，他的潜力很高但由于过于年轻，世界杯对其是个不小的考验。</p>
                </div>
              </li>
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_xiaoshumeiqieer72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">小舒梅切尔</h3>
                  <p class="p-info">效力于莱斯特城的小舒梅切尔，是莱斯特城神奇夺冠的功臣之，他是一名扑救能力和指挥防守都很不错门将。</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="g-square">
          <span class="tag">其他主要球员</span>
          <p class="p-txt">多尔贝（阿贾克斯）、沃斯（塞尔塔）、克亚尔（塞尔维亚）、波尔（莱比锡）、赫伊别尔 （南安普顿）、本特纳（罗森博格）。</p>
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
