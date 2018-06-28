<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection"> 
    <meta content="email=no" name="format-detection">
    <title>166彩票</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/transfer-page.min.css');?>">
</head>
<body>
    <div class="wrapper p-transfer">
        <div class="p-transfer-slide">
            <div class="swiper-container swiper-container-horizontal" data-swiper="[object Object]">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper" style="transition-duration: 0ms; transform: translate3d(-5396px, 0px, 0px);"><div class="swiper-slide swiper-slide-duplicate" data-swiper-slide-index="3" style="width: 1349px;">
                        <img src="<?php echo getStaticFile('/caipiaoimg/static/img/transfer-img4.png');?>" width="100%" alt="提款迅速，提款急速到账">
                    </div>
                    <!-- Slides -->
                    <div class="swiper-slide" data-swiper-slide-index="0" style="width: 1349px;">
                        <img src="<?php echo getStaticFile('/caipiaoimg/static/img/transfer-img1.png');?>" width="100%" alt="166彩票网，手机购彩新模式，足不出户中大奖">
                    </div>
                    <div class="swiper-slide" data-swiper-slide-index="1" style="width: 1349px;">
                        <img src="<?php echo getStaticFile('/caipiaoimg/static/img/transfer-img2.png');?>" width="100%" alt="购彩方便，O2O购彩，连接您与投注站">
                    </div>
                    <div class="swiper-slide swiper-slide-prev" data-swiper-slide-index="2" style="width: 1349px;">
                        <img src="<?php echo getStaticFile('/caipiaoimg/static/img/transfer-img3.png');?>" width="100%" alt="领奖安全，奖金自动入账">
                    </div>
                    <div class="swiper-slide swiper-slide-active" data-swiper-slide-index="3" style="width: 1349px;">
                        <img src="<?php echo getStaticFile('/caipiaoimg/static/img/transfer-img4.png');?>" width="100%" alt="提款迅速，提款急速到账">
                    </div>
                <div class="swiper-slide swiper-slide-duplicate swiper-slide-next" data-swiper-slide-index="0" style="width: 1349px;">
                        <img src="<?php echo getStaticFile('/caipiaoimg/static/img/transfer-img1.png');?>" width="100%" alt="166彩票网，手机购彩新模式，足不出户中大奖">
                    </div></div>
            </div>
            <div class="swiper-pagination"><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span></div>
        </div>
        <a href="/app/download?cpk=10062" class="btn-dlkhd">下载客户端<s>2.35M</s></a>
        <a href="javascript:;" class="lnk-goon" id="goBack">继续访问电脑版</a>
        <p class="tel">客服热线 400-690-6760</p>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js')?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/swiper.jquery.min.js')?>"></script>
    <script>
        $(function(){
            var backUrl = "<?php echo $backUrl; ?>";

            var mySwiper = new Swiper ('.swiper-container', {
                // Optional parameters
                autoplay : 3000,
                loop : true,
                pagination: '.swiper-pagination'
            })
            
            // Android 2.3.x 动画退化处理
            function decideAndroid23(ua) {
                ua = (ua || navigator.userAgent).toLowerCase();
                return ua.match(/android.2\.3/) ? true : false;
            }

            // 判断是否是IOS
            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
                document.querySelector('.wrapper').className += " ios";
            }

            if( decideAndroid23() ){
                $(".wrap").addClass("android23");
            }   

            // 返回
            $('#goBack').on('click', function(){
                var exdate = new Date();
                var expiredays = 365;
                exdate.setDate(exdate.getDate() + expiredays);
                document.cookie="appIgnore" + "=" + escape('1') + ((expiredays==null) ? "" : ";expires=" + exdate.toGMTString()) + ";path=/";
                window.location.href = backUrl;
            })
               
        })
    </script>
    <script type="text/javascript" 
            src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/analyticstracking.js'); ?>"></script>
     <script> 
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?b3407c198af562ed4133afc4c88ca97a";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>