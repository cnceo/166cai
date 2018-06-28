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
    <title>竞彩足球玩法进阶</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/lottery-advanced.min.css');?>">
</head>
<body ontouchstart="">
    <div class="wrap">
        <ul class="course-list">
            <li><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/lottery-advanced01.jpg');?>" alt=""></li>
            <li class="swiper-container">
                <ul class="swiper-wrapper">
                    <li class="swiper-slide"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/s5-win-draw-fail.jpg');?>" alt=""></li>
                    <li class="swiper-slide"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/s4-concede-points.jpg');?>" alt=""></li>
                    <li class="swiper-slide"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/s3-all-in.jpg');?>" alt=""></li>
                    <li class="swiper-slide"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/s2-score.jpg');?>" alt=""></li>
                    <li class="swiper-slide"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/s1-half-all.jpg');?>" alt=""></li>                    
                </ul>
                <div class="swiper-pagination">
                    <span class="swiper-pagination-bullet"></span>
                    <span class="swiper-pagination-bullet"></span>
                    <span class="swiper-pagination-bullet"></span>
                    <span class="swiper-pagination-bullet"></span>
                    <span class="swiper-pagination-bullet"></span>
                </div>
            </li>
            <li><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/lottery-advanced02.jpg');?>" alt=""></li>
            <li><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/lottery-advanced03.jpg');?>" alt=""></li>
            <li><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/lottery-advanced04.jpg');?>" alt=""></li>
            <li><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/lottery-advanced05.jpg');?>" alt=""></li>
            <li><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/lottery-advanced06.jpg');?>" alt=""></li>
            <li><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/lottery-advanced07.jpg');?>" alt=""></li>
            <li><a href="javascript:;" onclick="bet.btnclick('42', 'jczq');" class="link"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lottery-advanced/button.jpg');?>" alt=""></a></li>
        </ul>
    </div>

    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/swiper.jquery.min.js');?>"></script>
    <script>

        $(function () {
            var swiper = new Swiper('.swiper-container', {
                autoplay:2000,
                loop: true,
                pagination : '.swiper-pagination',
                paginationClickable :true,
                centeredSlides: true,
                slidesPerView :1.15,
                spaceBetween: '4%',
            });
        })


    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>