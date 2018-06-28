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
    <title>合买入门指南</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/hemai.min.css');?>">
</head>
<body>
    <div class="wrap">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <!-- Slides -->
                <div class="swiper-slide swiper-slide1">
                    <div class="slide-txt">
                        <p>毛主席说：人多力量大，合买的方式既能用小资金共同购买大额彩票，又能提高参与者的中奖机率、降低个人全购高金额彩票的风险。</p>
                    </div>
                </div>
                <div class="swiper-slide swiper-slide2">
                    <h2 class="slide-title">我要发合买</h2>
                    <div class="slide-img">
                        <div class="step one"></div>
                        <div class="step two"></div>
                        <div class="step three"></div>
                        <div class="step four"></div>
                    </div>
                </div>
                <div class="swiper-slide swiper-slide3">
                    <h2 class="slide-title">我要跟单</h2>
                    <div class="slide-img">
                        <div class="step one"></div>
                        <div class="step two"></div>
                        <div class="step three"></div>
                        <div class="step four"></div>
                    </div>
                </div>
                <div class="swiper-slide swiper-slide4">
                    <h2 class="slide-title">论大神的自我修养：需注重三点</h2>
                    <div class="bg-cicle"></div>
                    <div class="slide-img">
                        <div class="oval one">
                            <h3>注重发单时间</h3>
                            <p>方案发起越早，可以跟单的时间越长，方案满员率越高。</p>
                        </div>
                        <div class="oval two">
                            <h3>注重认购或保底金额</h3>
                            <p>发起人认购与保底金额越多给跟单人信心越大。</p>
                        </div>
                        <div class="oval three">
                            <h3>注重战绩积累</h3>
                            <p>优秀的战绩可以获得更多彩民的关注，赢得更多彩民参与合买。</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide swiper-slide5">
                    <h2 class="slide-title">辨别优质大神，跟单分合买大奖</h2>
                    <div class="bg-cicle"></div>
                    <div class="slide-img">
                        <div class="oval one">
                            <h3>看战绩：实力标志</h3>
                            <p>发起人战绩越高，说明以往中奖次数越多值得信任。</p>
                        </div>
                        <div class="oval two">
                            <h3>看进度与人气：大众认可</h3>
                            <p>方案参与人数越多，表示方案的认同度越高。</p>
                        </div>
                        <div class="oval three">
                            <h3>看个人主页：一目了然</h3>
                            <p>可以详细的查看发起人以往的合买记录。</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide swiper-slide6">
                    <h2 class="slide-title">合买奖金如何分</h2>
                    <div class="slide-img">
                        
                    </div>
                </div>
            </div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/swiper.jquery.min.js');?>" type="text/javascript"></script>
    <script>
        $(function(){
            var mySwiper = new Swiper ('.swiper-container', {
                // Optional parameters
                direction: 'vertical',
                heigth: '100%',
                nextButton:'.swiper-button-next',
                onInit: function(swiper){
                  $('.swiper-slide1').addClass('animation');
                },
                onSlideChangeEnd: function(swiper){
                    var that = 'swiper-slide' + (swiper.activeIndex + 1);
                    $('.' + that).addClass('animation');
                }
            })
            
            // Android 2.3.x 动画退化处理
            function decideAndroid23() {
                var ua = (ua || navigator.userAgent).toLowerCase();
                return ua.match(/android.2\.3/) ? true : false;
            }

            if( decideAndroid23() ){
                $(".wrap").addClass("android23");
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>