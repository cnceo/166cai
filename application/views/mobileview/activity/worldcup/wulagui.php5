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
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/wulagui" class="tab cur">乌拉圭</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/keluodiya" class="tab">克罗地亚</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/danmai" class="tab">丹麦</a>
        <a href="/<?php echo $agent?>/activity/worldcup/qdxl/other" class="tab">其它</a>
    </nav>
      <div class="main">
        <!-- 球队信息 -->
        <div class="g-team-head">
          <div class="team-pic"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/flag_wulagui.png');?>" alt=""></div>
          <p class="p-txt">乌拉圭世界杯最佳成绩是1930年和1950年的冠军，过去两届世界杯，老帅塔瓦雷斯带队一次闯入四强，一次从死亡之组突围，不可谓不成功。</p>
        </div>
        <!-- 主要球星 -->
        <div class="g-square">
          <span class="tag">主要球星</span>
          <div class="main-star">
            <ul class="star-list">
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_suyaleisi72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">苏亚雷斯</h3>
                  <p class="p-info">苏亚雷斯盘带、射门技术一流，既可以出任中锋也能在前场边路活动，是一位难得全能型攻击手。除了带球突破和射门之外，苏亚雷斯还能为队友输送炮弹。他捕捉机会的能力超强，是不折不扣禁区内的终结者。</p>
                </div>
              </li>
              <li class="star-list-1">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/world-cup-news/star/ava_kawani72.png');?>" alt="">
                <div class="info">
                  <h3 class="name">卡瓦尼</h3>
                  <p class="p-info">作为乌拉圭的主力前锋，卡瓦尼不但有着灵活的拿球技术以及冲击强度，还有着顶级的射门嗅觉和抢点能力。即可当边锋也可以当中锋，身体素质优势大，脚下技术也不错，而且活动范围广，是一位比较全面的攻击手。</p>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- 经典时刻 -->
        <div class="g-square">
          <span class="tag red">经典时刻</span>
          <p class="p-txt">2010年南非世界杯，乌拉圭队一路过关斩将闯入四分之一决赛，面对对手加纳队，前锋苏亚雷斯用“上帝之手”使乌拉圭队起死回生，并最终在点球大战中淘汰加纳队，乌拉圭队时隔40年重回4强。</p>
          <p class="p-txt">2014年巴西世界杯苏亚雷斯小组赛“生吃”基耶利尼，乌拉圭一球气走意大利。两届杯赛，苏亚雷斯两次神操作太过吸睛，以至于人们甚至忽略了他8场世界杯已经打进5球的事实。</p>
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
