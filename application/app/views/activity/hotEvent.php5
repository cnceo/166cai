<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <meta name="apple-itunes-app" content="app-id=myAppStoreID, affiliate-data=myAffiliateData, app-argument=myURL">
    <title>热门赛事推荐</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css')?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/hot-events.min.css')?>">
</head>
<body>
    <div class="wrapper hot-events">
        <div class="hot-events-hd">
            <div class="team-logo">
                <img src="/caipiaoimg/static/img/fb-bs.png" alt="">
            </div>
            <div class="events-info">
                <h1><span>世俱杯</span><strong class="team-name">巴萨</strong><u>VS</u><strong class="team-name">恒大</strong></h1>
                <time>12月17日 18:30</time>
            </div>
            <div class="team-logo">
                <img src="/caipiaoimg/static/img/fb-hd.png" alt="">
            </div>
        </div>
        
        <div class="hot-events-bd">
            <ul>
                <li>
                    <h2>胜负推荐</h2>
                    <div class="list-intro">巴塞罗那 胜</div>
                </li>
                <li>
                    <h2>比分推荐</h2>
                    <div class="list-intro">3:0 4:0</div>
                </li>
                <li>
                    <h2>基本分析</h2>
                    <div class="list-intro">巴萨大名单不包括拉菲尼亚，内马尔有伤在身；广州恒大阵中包括罗比尼奥、阿兰、保利尼奥都有欧战经验，此外还有韩国中卫金英权。巴萨此前从未和中国俱乐部有过正式比赛的交手；广州恒大第二次参加世俱杯，两年前他们在半决赛被拜仁3-0淘汰。</div>
                </li>
            </ul>
            <div class="hot-events-action btn-group">
                <a href="javascript:void(0)" onclick="bet.btnclick('42', 'jczq');" class="btn bet-now">立即投注</a>
            </div>          
        </div>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>