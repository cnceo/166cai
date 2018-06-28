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
  <title>免费送彩票</title>
  <link rel="stylesheet" href="<?php echo getStaticFile("/caipiaoimg/static/css/active/".$css);?>">
</head>
<body ontouchstart="">
  <div class="wrap">
    <div class="wrap-content">
        <!-- 领取前 -->
        <div class="dkw-form">
        <?php if ($rpbox) {?>
          <div class="rp-box">
            <em>&yen;<b>188</b></em>
            <small>彩票红包</small>
          </div>
        <?php }?>
          <div class="dkw-form-item">
            <input type="tel" name="phone-num" placeholder="输入手机号，领取红包">
          </div>
          <p class="p-false" id="phone-error"></p>
          <div class="dkw-form-item yzm">
              <input type="tel"name="input-txyzm" placeholder="输入4位验证码" class="input-yzm">
              <img id="imgCaptcha" src="<?php echo $this->config->item('pages_url'); ?>ios/activity/captcha" alt="">
              <a href="javascript:;" class="change-img" id="change_imgCaptcha">换一张</a>
          </div>
          <p class="p-false" id="yzm-error"></p>
          <a href="javascript:;" class="btn-click" id="btn-click-attend">领取红包</a>
        </div>
        <!-- 领取前 -->
        <!-- 领取后 -->
        <div class="dkw-form dkw-result" style="display: none;">
        	<?php if ($rpbox) {?>
        	<div class="rp-box"><em><b></b></em></div>
        	<?php }else {?>
        	<h2></h2>
			<?php }?>
            <p class="tips-txt">短信已发送至手机，<br>您可点击短信中的链接下载APP</p>
            <a href="<?php echo $this->config->item('pages_url'); ?>ios/download/?c=<?php echo $version?>" target="_self" class="btn-click" id="btn-click">立即下载APP（2.35M）</a>
            <!-- 按钮禁用样式添加类名 btn-click-dis -->
            <p class="tips-txt2">仅限手机号<span id="sendPhoneNum"></span>使用</p>
        </div>
        <!-- 领取前 -->
    </div>
    <div class="plus-rule">
		<ol class="rule-overflow-y">
			<li data-num='1'>活动时间：<?php echo date('Y.m.d', strtotime($startTime))?>-<?php echo date('Y.m.d', strtotime($endTime))?></li>
			<li data-num='2'>活动限新用户参加，进入活动页面，验证手机号后即可领取红包，每位用户限领一次。</li>
			<li data-num='3'>188元红包价值如下：<br>a. 3元注册红包（实名认证后可用）<br>b. 2元红包（充值20元及以上可用），5个<br>c. 5元红包（充值50元及以上可用），5个<br>d. 10元红包（充值100元及以上可用），5个<br>e. 20元红包（充值200元及以上可用），5个</li>
			<li data-num='4'>红包有效期为30天，逾期未使用的红包将被系统收回。</li>
			<li data-num='5'>充值时手动勾选充值红包即可使用。</li>
			<li data-num='6'>活动充值与赠送的红包均不能提现，只能用于购彩，中奖奖金可提现。</li>
			<li data-num='7'>活动过程中如用户通过不正当手段领取彩金，166彩票网有权不予赠送、限制提款、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。</li>
			<li data-num='8'>关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
		</ol>
      <div class="rule-arrow">规则</div>
    </div>
    <div class="rule-bg"></div>
  </div>
  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>

        
        require(['//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/config.js'], function() {

            require(['Zepto', 'basic', 'ui/loading/src/loadingfix', 'ui/tips/src/tipsfix'], function($, basic, loading, tips) {
			  $(function() {
				  $('input[name="phone-num"]').on('click', function(){
					  $('#phone-error').hide().empty();
				})
				$('input[name="input-txyzm"]').on('click', function(){
					  $('#yzm-error').hide().empty();
				})
				// 发送验证码
			      $('#sendCaptcha').on('tap', function(){
			          // 手机号码格式检查
			          var phone = $('input[name="phone-num"]').val();
			          var imgCaptcha = $('input[name="input-txyzm"]').val();
			
			          if(phone == '')
			          {
			        	  $('#phone-error').show().html('请输入手机号码');
			              return false;
			          }
			
			          if(imgCaptcha == '')
			          {
			        	  $('#yzm-error').show().html('请输入图形验证码');
			              return false;
			          }
			
			          if( /1\d{10}$/.test(phone) ){
			              $.ajax({
			                  type: "post",
			                  url: '/ios/activity/sendCaptcha',
			                  data: {phone:phone,imgCaptcha:imgCaptcha},                   
			                  success: function (data) {
			                      var data = $.parseJSON(data);
			                      if(data.status == '1')
			                      {
			                          count();
			                      }
			                      else
			                      {
			                    	  $('#yzm-error').show().html(data.msg);
			                      }
			                  },
			                  error: function () {
			                	  $('#yzm-error').show().html('网络异常，请稍后再试');
			                  }
			              });
			          }
			          else
			          {
			        	  $('#phone-error').show().html('手机号码格式错误');
			              return false;
			          }
			
			      }); 
			
			      // 领取红包
			      var closeTag = true;
			      $('#btn-click-attend').on('tap', function(){
			          var phone = $('input[name="phone-num"]').val();
			          var imgCaptcha = $('input[name="input-txyzm"]').val();
			
			          try{
			              var channel = android.getAppChannel();
			          }catch(e){
			              var channel = '0';
			          }
			
			          if(phone == '')
			          {
			        	  $('#phone-error').show().html('请输入手机号码');
			              return false;
			          }
			
			          if(imgCaptcha == '')
			          {
			        	  $('#yzm-error').show().html('请输入图形验证码');
			              return false;
			          }
			
			          var showLoading = $.loading().loading("mask");
			
			          $.ajax({
			              type: "post",
			              url: '/ios/activity/outerAttend',
			              data: {phone:phone,imgCaptcha:imgCaptcha,channel:channel,smsid:<?php echo $smsid?>},                   
			              success: function (data) {
			                  showLoading.loading("hide");
			                  var data = $.parseJSON(data);
			
			                  if(data.status == '200')
			                  {
			                      $('#sendPhoneNum').html(phone);
			                      $('.hb-wai, .dkw-form').hide();
			                      $('.dkw-result').show().find('.rp-box em b, h2').html('红包领取成功');
			                  }else if(data.status == '300'){
			                      closeTag = true;
			                      $('input[name="input-txyzm"]').val('');
			                      refreshCaptcha();
			                      $('#yzm-error').show().html(data.msg);
			                  }else if(data.status == '400'){
			                      $('#sendPhoneNum').html(phone);
			                      $('.hb-wai, .dkw-form').hide();
			                      $('.dkw-result').show().find('.rp-box em b, h2').html('已领取过红包');
			                  }else if (data.status == '100') {
			                	  closeTag = true;
			                      $('#phone-error').show().html(data.msg);
				              }else
			                  {
			                      closeTag = true;
			                      $('#yzm-error').show().html(data.msg);
			                  }
			              },
			              error: function () {
			                  closeTag = true;
			                  showLoading.loading("hide");
			                  $('#yzm-error').show().html(data.msg);
			              }
			          });
			      }); 
			
			   // 刷新图形验证码
			      function refreshCaptcha(){
			          $('#imgCaptcha').attr('src', '/ios/activity/captcha?v=' + Math.random());
			      }
			
			      // 刷新图形验证码
			      $('#change_imgCaptcha').on('tap', function(){
			          refreshCaptcha();
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
			  })
            })

        })
  </script>
  <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>
