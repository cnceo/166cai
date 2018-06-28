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
<body ontouchstart="" class="theme-yellow">
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
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/keluodiya" class="tab cur">克罗地亚</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/danmai" class="tab">丹麦</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/other" class="tab">其它</a>
    </nav>
      <div class="main">
        <!-- 球队信息 -->
        <div class="g-team-head">
          <div class="team-pic"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/flag_keluodiya.png');?>" alt=""></div>
          <p class="p-txt">此番是克罗地亚第五次参加世界杯，最佳成绩是1998年法国世界杯。那是格子军首次参加世界杯，便取得了第三名的好成绩。</p>
        </div>
        <!-- 头号球星-->
        <div class="g-square">
          <span class="tag">头号球星</span>
          <div class="first-star">
            <h2 class="name">莫德里奇</h2>
            <p class="p-info">毫无疑问，虽然克罗地亚大牌很多，但最大牌的球员当属莫德里奇，他是格子军的队长兼核心，莫德里奇现年32岁，司职中场，曾代表克罗地亚出场103次，参加过两届世界杯，以及三届欧洲杯。</p>
            <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_modeliqi302.png');?>" alt="">
          </div>
        </div>

        <!-- 其他主要球员 -->
        <div class="g-square">
          <span class="tag">其他主要球员</span>
          <p class="p-txt">苏巴希奇、洛夫伦、布罗佐维奇、拉基蒂奇、科瓦契奇、曼朱基齐、佩里西奇......</p>
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
