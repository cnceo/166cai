<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="author" content="weblol">
        <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui">
        <meta name="format-detection" content="telephone=no, email=no">
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
        <meta name="apple-mobile-web-app-title" content="166彩票">
        <title>足球知识知多少</title>
        <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/answer-sheet.min.css') ?>">
    </head>
    <body ontouchstart="">
        <div class="wrap">
            <div class="top">
                <?php if(!$userid){ ?>
                <?php if($count == 0 || empty($config) || (!empty($config) && $config['start_time']>date("Y-m-d H:i:s"))){ ?>
                <img src="/caipiaoimg/static/images/active/answer-sheet/answer.png" alt="">
                <?php }else{ ?>
                <img src="/caipiaoimg/static/images/active/answer-sheet/answer-s.png" alt="">
                <div class="banner-text">
                    <?php $wenan = explode('*', $config['titleDesc']); ?>
                    <h2><?php echo $wenan[0]; ?></h2>
                    <span>— <?php echo $wenan[1]; ?> —</span>
                </div>
                <?php } ?>
                <?php } ?>
            </div>
        </div>
        <?php if(!$userid){ ?>
        <div class="button">
            <?php if($count == 0 || (!empty($config) && $config['start_time']>date("Y-m-d H:i:s"))){ ?>
            <a class="btn disabled" href="javascript:;">答题未开始</a>
            <?php }elseif(empty($config)){ ?>
            <a class="btn disabled" href="javascript:;">本轮答题结束</a>
            <?php }else{ ?>
            <a class="btn" href="javascript:;" id="ksdt">开始答题</a>
            <?php } ?>
        </div>
        <?php } ?>

        <!-- 红包-->
        <?php if($userid){ ?>
        <div class="pop-mask" style="display:block;">
            <?php if($has == 0){ ?>
            <div class="pop-redbag gold">
                <?php if(empty($redpack)){ ?>
                <div class="text">
                  <span class="aiya mb40">哎呀,本次答对<?php echo $totalvalue; ?>道题......</span>
                  <h3 class="mb50">没有获得红包</h3>
                  <a href="/app/activity/worldcup2018" class="btn yellow staff">返回活动首页</a>
                </div>
                <?php }else{ ?>
                <div class="text">
                    <span class="aiya">太厉害了，答对<?php echo $totalvalue; ?>道题！请收下</span>
                    <h3><?php $type ="购彩";if($redpack['p_type']==2)$type="充值";if($redpack['p_type']==1)$type="彩金";echo ($redpack['money']/100)."元".$type.'红包'; ?></h3>
                    <a href="/app/redpack/index/<?php echo $token; ?>" class="btn yellow staff mb30">去看看</a>
                    <a href="/app/activity/worldcup2018" class="back"><返回活动首页</a>
                </div>
                <?php } ?>
            </div>
            <?php }else{ ?>
            <div class="pop-redbag">
                <div class="text">
                  <span class="aiya mb40">您已参与过本次答题</span>
                  <h3 class="mb50">请下次再来~</h3>
                  <a href="/app/activity/worldcup2018" class="btn yellow staff">返回活动首页</a>
                </div>
            </div>    
            <?php } ?>


        </div>
        <?php } ?>


<?php if(!$userid){ ?>
<?php if(!empty($config) && $config['start_time']<date("Y-m-d H:i:s")){ ?>
        <div class="plus-rule top0">
            <ol class="rule-overflow-y">
                <?php echo $config['rule'] ?>
            </ol>
            <div class="rule-arrow">规则</div>
        </div>
        <div class="rule-bg"></div>
<?php } ?>
<?php } ?>








    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js')?>"></script>
    <script>

        $('.rule-arrow').click(function () {

            if ($('.plus-rule').hasClass('plus-rule-show')) {
                $('.plus-rule').removeClass('plus-rule-show');
            } else {
                $('.plus-rule').addClass('plus-rule-show');
            }
        })

        $("#ksdt").click(function () {
            var uid = <?php echo $this->uid?$this->uid:0?>;
            if(uid==0){
                var backUrl = window.location.href;
                android.relogin(backUrl)
                return false;
            }
            window.location.href = "<?php echo $config['questionUrl'].'?sojumpparm='.$token?>";
        })


    </script>
    <script>
//        var psHeight = $('.plus-rule').height();
//        $('.plus-rule').css({
//            'top': '-' + psHeight + 'px'
//        });
//        setTimeout(function () {
//            $('.plus-rule').css({
//                'transition': 'all ease-in 400ms',
//                '-webkit-transition': 'all ease-in 400ms'
//            })
//        }, 2000)
//        $('.plus-rule').on('click', '.rule-arrow', function () {
//            var thisParent = $(this).parents('.plus-rule');
//            thisParent.find('.rule-overflow-y').show();
//            thisParent.toggleClass('plus-rule-show');
//            if (!thisParent.hasClass('plus-rule-show')) {
//                $('.plus-rule').css({
//                    'top': '-' + psHeight + 'px'
//                })
//            } else {
//                $('.plus-rule').css({
//                    'top': 0
//                })
//            }
//            $('body').toggleClass('overflowScroll');
//            return false;
//        })
//        $('.plus-rule + .rule-bg').on('click', function () {
//            $('.plus-rule').css({
//                'top': '-' + psHeight + 'px'
//            })
//            $('.plus-rule').removeClass('plus-rule-show');
//            thisParent.find('.rule-overflow-y').hide();
//            $('body').removeClass('overflowScroll');
//        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>
