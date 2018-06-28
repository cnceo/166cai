<!DOCTYPE HTML>
<!--[if lt IE 7]><html class="ie6"><![endif]-->
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if gte IE 9]><html class="ie9"><![endif]-->
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
        <title>服务：手机服务新模式，足不出户中大奖-166彩票官网</title>
        <link rel="stylesheet" type="text/css" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/active/166cai.min.css') ?>" />
        <script type="text/javascript">
            var baseUrl = '<?php echo $this->config->item('base_url'); ?>';
        </script>
        <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js'); ?>" type="text/javascript"></script>
        <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
        <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery.fullPage.min.js'); ?>" type="text/javascript"></script>
    </head>
    <body>
        <?php if (empty($this->uid)): ?>
            <div class="top_bar">
                <?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
            </div>
        <?php else: ?>
            <div class="top_bar">
                <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="wrapper" id="fullpage">
        <!-- section1 -->
        <div class="section section1">
            <div class="bg"></div>
            <div class="main">
                <span class="pic">
                    <img src="/caipiaoimg/v1.1/img/active/166cai/section1-img.png" alt="" width="665" height="414">
                </span>
                <div class="con">
                    <div class="title">
                        <span class="logo">166彩票</span>
                        <div class="txt">
                            <h2 class="txt-h2">手机约彩新模式 足不出户中大奖</h2>
                            <h3 class="txt-h3">以平台为依托，实现投注站与彩民在线上的对接<br>实现“彩民—平台—投注站”的三方模式。</h3>                 
                        </div>
                    </div>
                    <div class="download">
                        <div class="btn-grp">
                            <a href="<?php echo $this->config->item('base_url')?>app/download/?c=10047" class="btn-down-android" target="_self"><i class="icon i-android"></i><span class="a-txt">Android版下载</span>
                                <span class="layer-down"><i class="icon i-down"></i><span class="a-txt">2.35M</span></span></a>
                            <a href="https://itunes.apple.com/cn/app/166cai-piao-shuang-se-qiu/id1108268497?mt=8" class="btn-down-iphone mt20" target="_blank"><i class="icon i-iphone" target="_self"></i><span class="a-txt" style="color: #333;">iPhone版下载</span><span class="layer-down"><i class="icon i-down"></i><span class="a-txt">25.9M</span></span></a>
                        </div>
                        <span class="qr-code">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/166cai/qr-code.png') ?>" alt="" width="121" height="121">
                        </span>
                    </div>
                </div>
                <div class="ele-cloud cloud-1"></div>
                <div class="ele-cloud cloud-2"></div>
                <div class="ele-cloud cloud-3"></div>
                <div class="ele-cloud cloud-4"></div>
                <div class="ele-cloud cloud-5"></div>
            </div>
        </div>
        <!-- section2 -->
        <div class="section section2">
            <div class="bg"></div>
            <div class="main">
                <div class="title">
                    <h2 class="txt-h2">您手机下单  我去投注站</h2>
                    <h3 class="txt-h3">以平台为依托，实现投注站与彩民在线上的对接<br>实现“彩民—平台—投注站”的三方模式。</h3>
                </div>
                <div class="pic">
                    <div class="stage"></div>
                    <div class="p-1"></div>
                    <div class="p-2"></div>
                    <div class="p-3"></div>
                    <div class="p-4"></div>
                </div>
            </div>
        </div>
        <!-- section3 -->
        <div class="section section3">
            <div class="bg"></div>
            <div class="main">
                <div class="title">
                    <h2 class="txt-h2">安全有保障  服务新趋势</h2>
                    <h3 class="txt-h3">登陆密码保障，购彩实名制、中大奖全程协助领取，为您打造<br>最安全的服务平台，让您约彩无忧、领奖无忧~</h3>                
                </div>
                <div class="pic">
                    <div class="stage"></div>
                    <div class="safe"></div>
                    <div class="star"></div>
                </div>
            </div>
        </div>
        <div class="section footer fp-auto-height" style="margin-top: 0">
            <div class="main">
                <p>166彩票提醒：理性购彩，热爱公益  我们承诺不向未满18周岁的青少年出售彩票！</p>
                <p>版权所有 © 上海彩咖网络科技有限公司  ICP证沪B2-20120099   在线客服  客服热线：400-000-1234</p>
            </div>
        </div>
    </div>
    <script type="text/javascript">
            $(document).ready(function () {
                $(".arrow").click(function () {
                    $.fn.fullpage.moveSectionDown();
                });
            });
            $('#fullpage').fullpage({
                'verticalCentered': false,
                'css3': true,
                'anchors': ['page1', 'page2', 'page3', 'page4'],
                'navigation': true,
                'navigationPosition': 'right'
            });
    </script>
    <div class="hide">
        <?php $this->load->view('v1.1/elements/common/footer_academy'); ?>
    </div>
</body>
</html>