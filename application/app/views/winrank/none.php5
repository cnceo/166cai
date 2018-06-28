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
  <title>大神排行榜</title>
  <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
  <link rel="stylesheet" href="/caipiaoimg/static/css/active/ranking-list.min.css">
  <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/account-detail.min.css');?>">
</head>
<body ontouchstart="">
    <div class="wrap">
        <div class="wrap-content">
            <div class="top-banner">
                <img src="//888.166cai.cn<?php echo $banner?>">
                <!--<img src="/caipiaoimg/static/images/active/ranking-list/banner.jpg" alt="">-->
            </div>
            <!--<div class="list-history">
                <span <?php /*echo $is_last ? 'onclic="getPeriod('.$is_last.')"' : 'class="disabled"'*/?> ><i class="left-arrow"></i></span>
                <h2 class="tit">中奖排行榜（<?php /*echo $time_limit*/?>）</h2>
                <span <?php /*echo $is_next ? 'onclic="getPeriod('.$is_next.')"' : 'class="disabled"'*/?>><i class="right-arrow"></i></span>

            </div>-->
            <div class="aboutMe">
                <div class="item">
                    <span class="s-txt">我的排名</span>
                    <span class="s-val"><?php echo $rank_id?></span>
                </div>
                <div class="item">
                    <span class="s-txt">我的累计中奖</span>
                    <span class="s-val"><?php echo $count_prize;?></span>
                </div>
                <div class="item">
                    <span class="s-txt"><?php  echo $is_cstate ? '我的彩金奖励' : '我的预计奖励'?></span>
                    <span class="s-val red"><?php echo $expect_bonuses;?>元</span>
                </div>
            </div>
            <div class="ranking-list">
                <div class="th">
                    <span class="tit">中奖排行榜</span>
                    <span class="last-rank red">上期回顾></span>
                </div>
                <div class="g-table">
                    <table class="no-bb">
                        <thead>
                            <tr>
                                <th>排名</th>
                                <th>用户名</th>
                                <th>累计中奖</th>
                                <th><?php  echo $is_cstate ? '彩金奖励' : '预计奖励'?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="no-bb">
                                <td colspan="4" class="no-bb">
                                    <img src="/caipiaoimg/static/images/active/ranking-list/big-prize-cup.png" alt="">
                                    <span class="nobody">还没人上榜哦</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="placeholder"></div>
            <div class="g-btn">
                <?php
                if($is_stop){
                    if($platform == 'IOS' || $platform == 'Android'){
                        echo '<a href="javascript:void (0)" class="btn end popOpen" style="background: red;">立即投注赢彩金</a>';
                    }else{
                        echo '<a href="https://8.166cai.cn" class="btn end popOpen" style="background: red;">立即投注赢彩金</a>';
                    }
                }
                ?>

                <?php if(!$is_stop){?>
                    <a href="" class="btn end">
                        <span>活动已截止<?php  if($is_cstate){ echo '，已派奖';}?></span>
                        <span class="s-des"><?php if(!$is_cstate){echo  '预计'.$predict_time.'派奖';}?></span>
                    </a>
                <?php  } ?>
            </div>
        </div>
        <div class="plus-rule">
            <ol class="rule-overflow-y">
            <?php echo $rule;?>
            </ol>
            <div class="rule-arrow">规则</div>
        </div>
        <div class="rule-bg"></div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        // 基础配置
        require.config({
            baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
            paths: {
                "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
                "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
                'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
            }
        })
        require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){

            var func = "<?php echo $func;?>";
            var isLast = "<?php echo $is_last?>";
            $(".last-rank").click(function () {
                if(isLast > 0){
                    location.href = '/app/activityphb/'+func+'/'+isLast+'/history';
                }else{
                    $.tips({
                        content: '暂无往期数据',
                        stayTime: 2000
                    })
                }
            });
      
            var psHeight = $('.plus-rule').height();
            $('.plus-rule').css({
                'top': '-' + psHeight + 'px'
            });
            setTimeout(function () {
                $('.plus-rule').css({
                    'transition': 'all ease-in 400ms',
                    '-webkit-transition': 'all ease-in 400ms'
                })
            }, 2000)
            $('.plus-rule').on('click', '.rule-arrow', function() {
                var thisParent = $(this).parents('.plus-rule');
                thisParent.find('.rule-overflow-y').show();
                thisParent.toggleClass('plus-rule-show');
                if(!thisParent.hasClass('plus-rule-show')) {
                    $('.plus-rule').css({
                        'top': '-' + psHeight + 'px'
                    })
                } else {
                    $('.plus-rule').css({
                        'top': 0
                    })
                }
                $('body').toggleClass('overflowScroll');
                return false;
            })
            $('.plus-rule + .rule-bg').on('click', function () {
                $('.plus-rule').css({
                    'top': '-' + psHeight + 'px'
                })
                $('.plus-rule').removeClass('plus-rule-show');
                thisParent.find('.rule-overflow-y').hide();
                $('body').removeClass('overflowScroll');
            })

            $('body').on('click', '.popOpen', function(){
                try{
                    // 点击事件
                    android.umengStatistic('useRedpack');
                }catch(e){
                    // ...
                }
                var $_this = $(this);
                if($(this).hasClass('disabled')){
                    return false;
                }
                var lids = "<?php echo $config['lids']?>";
                $.ajax({
                    type: 'post',
                    url: '/app/activityphb/bettingWindow',
                    data: {lids:lids},
                    success: function (response) {
                        var response = $.parseJSON(response);
                        if(response.status == 1){
                            $('body').append(response.data);
                            $('#popTitle').html( '<small>可投注以下彩种，请选择</small>');
                            try {
                                android.setFresh('0')
                            } catch (e) {
                                console.log(e)
                            }
                        }else{
                            $.tips({
                                content: response.msg,
                                stayTime: 2000
                            })
                        }
                    },
                    error: function () {
                        $.tips({
                            content: '操作失败，请联系客服',
                            stayTime: 2000
                        })
                    }
                });
            });
            //关闭弹窗
            $('body').on('click', '.popcancel', function(){
                $('.rp-go').remove();
                try {
                    android.setFresh('1')
                } catch (e) {
                    console.log(e)
                }
            });
        });
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>
