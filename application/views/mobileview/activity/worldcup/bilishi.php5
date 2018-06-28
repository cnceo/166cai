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
<body ontouchstart="" class="theme-green">
  <div class="wrap">
    <nav class="tab-list js-tab">
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/deguo" class="tab">德国</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/baxi" class="tab">巴西</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/putaoya" class="tab">葡萄牙</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/agenting" class="tab">阿根廷</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/bilishi" class="tab cur">比利时</a>
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
          <div class="team-pic"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/flag_bilishi.png');?>" alt=""></div>
          <p class="p-txt">作为夺冠热门之一，他们不仅实力超群，而且经验丰富，比利时队在世界杯上的最佳战绩是在1986年世界杯上杀入四强。在经历了2014年世界杯和2016年欧洲杯的磨砺之后，他们的目标，就是夺得队史上第一个世界杯冠军！</p>
        </div>
        <!-- 主要阵容-->
        <div class="g-square">
          <span class="tag">主要阵容</span>
          <p class="p-txt">这支比利时，巨星太多，切尔西核心阿扎尔、曼城核心德布劳内和队长孔帕尼、曼联锋霸卢卡库、切尔西门神库尔图瓦……</p>
        </div>
        <!-- 主要球星 -->
        <div class="g-square">
          <span class="tag">主要球星</span>
          <div class="main-star">
            <ul class="star-list">
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_azhaer72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">阿扎尔</h3>
                  <p class="p-info">阿扎尔司职左边锋，以厉害的创造力、速度还有控球技术而闻名。</p>
                </div>
              </li>
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_debulaonei72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">德布劳内</h3>
                  <p class="p-info">德布劳内的组织能力、传中能力和定位球能力都是当今足坛的顶尖水准。</p>
                </div>
              </li>
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_lukaku72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">卢卡库</h3>
                  <p class="p-info">魔兽卢卡库作为纯正中锋，除了拥有强壮的身体和不俗的做球、拿球能力还有很好的脚下技术。</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
        
        <!-- 其他主要球员 -->
        <div class="g-square">
          <span class="tag">其他主要球员</span>
          <p class="p-txt">默滕斯（那不勒斯）、巴舒亚伊（多特）、卡拉斯科（大连一方）、费莱尼（曼联）、维特塞尔（权健）、孔帕尼（曼城）、维尔通亨（热刺）、库尔图瓦（切尔西）。</p>
        </div>
        <a class="g-btn" href="javascript:;">立即投注</a>
      </div>
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