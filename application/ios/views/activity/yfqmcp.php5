<?php $this->load->view('comm/header'); ?>
  <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/one.min.css');?>">
</head>
<body ontouchstart="">
  <div class="wrap">
    <div class="wrap-content">
        <!-- 领取前 -->
        <?php if(!$hasAttend): ?>
        <div class="dkw-form">
            <div class="hb" id="hb">
                <a href="javascript:void(0);" class="btn-click" id="btn-click">立即领取红包</a>
            </div>
        </div>
        <?php endif; ?>
        <!-- 领取后 -->
        <div class="dkw-form dkw-result hb-after"<?php if(!$hasAttend){ ?>style="display: none;"<?php }?>>
            <div class="rp-box">
              <em><b>已领取过红包</b></em>
            </div>
            <a href="javascript:;" class="btn-click" id="btn-click">查看红包</a>
        </div>

        <div class="note">
          <div class="note-title">166元大礼包</div>
          <div class="note-bd note-tab">
            <ul class="note-tab-hd">
              <li class="active"><label for="choose1">注册后第1～11天</label></li>
              <li><label for="choose2">第13～19天</label></li>
              <li><label for="choose3">第19～56天</label></li>
            </ul>
            <ul class="note-tab-bd">
              <li>
                <input id="choose1" type="radio" checked name="choose">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/one/hb-list1.png');?>" alt="">
              </li>
              <li>
                <input id="choose2" type="radio" name="choose">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/one/hb-list2.png');?>" alt="">
              </li>
              <li>
                <input id="choose3" type="radio" name="choose">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/one/hb-list3.png');?>" alt="">
              </li>
            </ul>
          </div>
        </div>
        <!-- 领取前 -->
    </div>
    <div class="plus-rule">
      <ol class="rule-overflow-y">
		<li>1、活动时间：<?php echo date('Y年m月d日', strtotime($startTime))?>至<?php echo date('Y年m月d日', strtotime($endTime))?>。</li>
        <li>2、活动限新用户参加, 注册实名认证后系统自动派发红包，每位用户限领一次。</li>
        <li>3、166元大礼包组成：<br>
        	满2元减1.99，1个<br>
        	2元红包（充30送2），1个<br>
        	2元红包（满30减2），3个<br>
        	2元红包（满30减2），1个（仅高频使用）<br>
        	2元红包（满30减2），1个（仅竞彩使用）<br>
        	3元红包（满60减3），4个<br>
        	5元红包（满100减5），4个<br>
        	5元红包（满100减5），1个（仅高频使用）<br>
        	5元红包（满100减5），1个（仅竞彩使用）<br>
        	10元红包（满200减10），5个<br>
        	15元红包（满300减15），4个
        </li>
        <li>4、红包有效期为7天，逾期未使用的红包将被系统收回。</li>
        <li>5、购彩或充值时可直接使用满足条件的红包。</li>
        <li>6、活动过程中如用户通过不正当手段领取红包和彩金，166彩票网有权收回赠送、限制提现、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。</li>
        <li>7、关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
			</ol>
      <div class="rule-arrow">规则</div>
    </div>
    <div class="rule-bg"></div>
  </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>"></script>
<script>
    // 基础配置
    require(['//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/config.js'], function() {
		require(['Zepto', 'basic', 'ui/loading/src/loadingfix', 'ui/tips/src/tipsfix'], function($, basic, loading, tips) {

        
            	var psHeight = $('.plus-rule').height(), closeTag = true;
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

                // 换一张图片
                var timer = null;
                $('.change-img').on('click', function () {
                  var self = $(this);
                  clearTimeout(timer);
                  self.addClass('change-rotate')
                  timer = setTimeout(function () {
                    self.removeClass('change-rotate');
                  }, 400)
                  return false;
                })

                $('.note-tab-hd').on('click', 'li', function () {
                  $(this).addClass('active').siblings().removeClass('active');
                })

                // 2345浏览器 虚拟键盘遮挡 BUG 修复
                if(navigator.userAgent.indexOf("Mb2345Browser") > -1) {
                  $('body').on('focus', 'input', function () {
                    iptScrollTop = $(window).scrollTop();
                    $('.wrap').css({
                      paddingBottom: '60px'
                    })
                    $('body,html').scrollTop($(document).height() - $(window).height());
                  }).on('blur', 'input', function () {
                    $('.wrap').css({
                      paddingBottom: '10px'
                    })     
                  })
                }

        $('.dkw-form').on('tap', '.btn-click', function(){
            var $this = $(this);
            var Bcp = $(this).parents('div');
            if(Bcp.hasClass('hb-after')){
            	window.webkit.messageHandlers.goRedpack.postMessage({});
            }else{
                if(closeTag){
                closeTag = false;
                    var showLoading = $.loading().loading("mask");
                    try{
                        var channel = android.getAppChannel();
                    }catch(e){
                        var channel = '0';
                    }
                    $.ajax({
                        type: "post",
                        url: '/ios/activity/innerAttend',
                        data: {channel:channel},                   
                        success: function (data) {
                            showLoading.loading("hide");
                            var data = $.parseJSON(data);

                            if(data.status == '200')
                            {
                            	$.tips({
                                    content:data.msg,
                                    stayTime:2000
                                });
                            	$('.dkw-form').hide();
                                $('.dkw-result').show();
                            }
                            else if(data.status == '100')
                            {
                                // 未登录
                                closeTag = true;
                                var backUrl = window.location.href;
                                window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                            }
                            else
                            {
                                closeTag = true;
                                $.tips({
                                    content:data.msg,
                                    stayTime:2000
                                });
                            }
                        },
                        error: function () {
                            closeTag = true;
                            showLoading.loading("hide");
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                }
            }       
        	})
    	})
    });

    
  </script>
</body>
</html>
