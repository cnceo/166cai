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
    <title>邀请好友赢千元彩金</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/join-us.min.css'); ?>"/>
</head>
<body ontouchstart="">
	<h1 style="display:none;">邀请好友赢千元彩金</h1>
	<div style='margin:0 auto;width:0px;height:0px;overflow:hidden;'>
		<img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lxShare.jpg');?>">
	</div>
    <div class="wrap join-wrap">
        <div class="join-hd">
            <h1>邀请好友赢千元彩金</h1>
            <p>好友还能免费领取188元红包</p>   
        </div>
        <div class="join-bd">
            <div class="join-invite">
                <!-- 第一步 start -->
                <form action="" class="join-invite-email">
                    <strong>您的好友赠送了您188元红包</strong>
                    <input type="tel" placeholder="请输入您的手机号" name="lxcheck">
                    <div class="form-vcode form-vcode-img">
                        <input type="text" placeholder="请输入图片验证码" c-placeholder="请输入图片验证码" name="imgCaptcha" data-rule="checkrsgcode" data-ajaxcheck='1' value="" />
                        <img id="captcha_reg" src="/mainajax/captcha?v=<?php echo time();?>" alt="">
                        <a href="javascript:;" class="change-img">换一张</a>
                    </div>
                    <div class="form-vcode">
                        <input type="text" name="joinCapche" placeholder="输入4位验证码">
                        <a href="javascript:;" class="lnk-getvcode _timer">获取验证码</a>
                        <span class="lnk-getvcode-disabled hide" style="display:none">重新获取<s>(<em id="_timer">60</em>)</s></span>
                    </div>
                    <a href="javascript:;" class="btn-get submit">马上领取188元红包</a>
                </form>
                <!-- 第一步 end -->
            </div>
        </div>
        <div class="join-ft">
            <p class="join-tips">每个手机号只能参加一次，详细规则见客户端</p>
            <p>活动最终解释权归166彩票网所有</p>
        </div>
    </div>
    
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/basic.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/ui/tips/src/tips.js');?>" type="text/javascript"></script>

    <?php $useragent = $_SERVER['HTTP_USER_AGENT'];?>
    <?php if( strpos($useragent, 'MicroMessenger') !== FALSE ): ?>
    <!-- 微信内 -->
    <script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript"></script>
    <script>
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: '<?php echo $signPackage["timestamp"];?>',
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
          'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo'
        ]
    });

    wx.ready(function () {
        // 分享到朋友圈
        wx.onMenuShareTimeline({
            title: '我邀请您加入166彩票，并向您扔了一个188元红包',
            link: '<?php echo $signPackage["url"];?>',
            imgUrl: "<?php echo getStaticFile('/caipiaoimg/static/images/active/lxShare.jpg');?>",
            success: function () { 
                // 用户确认分享后执行的回调函数
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
            }
        });

        // 分享给朋友
        wx.onMenuShareAppMessage({
            title: '我邀请您加入166彩票，并向您扔了一个188元红包',
            desc: '166彩票送壕礼，领取188元红包，下一个百万巨奖大户就是你！',
            link: '<?php echo $signPackage["url"];?>',
            imgUrl: "<?php echo getStaticFile('/caipiaoimg/static/images/active/lxShare.jpg');?>",
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () { 
                // 用户确认分享后执行的回调函数
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
            }
        });

        // 分享到QQ
        wx.onMenuShareQQ({
            title: '我邀请您加入166彩票，并向您扔了一个188元红包',
            desc: '166彩票送壕礼，领取188元红包，下一个百万巨奖大户就是你！',
            link: '<?php echo $signPackage["url"];?>',
            imgUrl: "<?php echo getStaticFile('/caipiaoimg/static/images/active/lxShare.jpg');?>",
            success: function () { 
               // 用户确认分享后执行的回调函数
            },
            cancel: function () { 
               // 用户取消分享后执行的回调函数
            }
        });

        // 分享到腾讯微博
        wx.onMenuShareWeibo({
            title: '我邀请您加入166彩票，并向您扔了一个188元红包',
            desc: '166彩票送壕礼，领取188元红包，下一个百万巨奖大户就是你！',
            link: '<?php echo $signPackage["url"];?>',
            imgUrl: "<?php echo getStaticFile('/caipiaoimg/static/images/active/lxShare.jpg');?>",
            success: function () { 
               // 用户确认分享后执行的回调函数
            },
            cancel: function () { 
                // 用户取消分享后执行的回调函数
            }
        });
    });
    </script>
    <?php endif; ?>
    <script>
    var _ss;//计算剩余的秒数
    var time;
    var disabled;

    function timer() {
    	_ss = 60;
    	name = $('._timer').data('freeze');
    	$(".vyzm").focus();
		$('._timer').addClass('disabled').hide();
		$('#_timer').parents('.lnk-getvcode-disabled').removeClass('hide');
		$(".ui-poptip-yuyin").show().parents('.form-item-con').addClass('zindex10');
		YzmClick();
		closeTimer();
		time = setInterval(function() {
	        console.log(1);
		    _ss -= 1;
		    if (_ss >= 0) {
		        $('#_timer').html(_ss);
		        return false;
		    }
		    $('#_timer').parents('.lnk-getvcode-disabled').addClass('hide').hide();
		    $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
		    $('._timer').removeClass('disabled').show();
		    closeTimer();
		    YzmClick();
		    return true;
		}, 1000);
	}

    function closeTimer(show) {
        clearInterval(time);
        if (show) {
            $('#_timer').parents('.lnk-getvcode-disabled').addClass('hide');
            $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
            $('._timer').removeClass('disabled').show();
            YzmClick();
        }
    }

    function YzmClick() {
        $(".vyzm").on('keydown', function () {
            $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
        });
        $('form .simu-select-med').on('click', function () {
            $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
        })
    }
    $(function(){
		var getYzmLook = false;
		$('.lnk-getvcode').click(function(){
			var self = $(this);
	        var lxcheck = $('.join-invite-email input[name="lxcheck"]').val();
	        if(!lxcheck){
	        	$.tips({content:'请输入手机号', stayTime:2000});
	        	return false;
	        }
	        if(!lxcheck.match(/1(\d{10})/) || lxcheck.length !== 11){
	        	$.tips({content:'请输入正确的手机号', stayTime:2000});
	        	return false;
	        }
	        var imgCaptcha = $('.join-invite-email input[name="imgCaptcha"]').val();
	        if(!imgCaptcha){
	        	$.tips({content:'请输入图形验证码', stayTime:2000});
	        	return false;
	        }
	        if (!getYzmLook) {
				getYzmLook = true;
	            $.ajax({
	                type: 'post',
	                url:  '/main/getPhoneCode/joinCapche',
	                data: {'phone':lxcheck, 'position':<?php echo $position['activity_captche']?>, phflag:1, code:imgCaptcha},
	                dataType: 'json',
	                success: function(response) {
	                	timer(self);	
	    	        	$('.lnk-getvcode-disabled').show();
	    	        	self.hide();
	                    if(response.status){
	                    	$('.form-join input[name="imgCaptcha"]').attr('data-ajaxcheck', '0');
	                    }else{
	                    	$('#captcha_reg').attr('src', '/mainajax/captcha?v=' + Math.random());
	                    	if (response.msg) {
	                    		$.tips({content:'请输入正确的图形验证码', stayTime:2000});
	                        	closeTimer(1);
	                        	$('#_timer').parents('.lnk-getvcode-disabled').addClass('hide').hide();
	                    	}

	                    }
	                    getYzmLook = false;
	                }
	            });
			}
	    });
	    $('.submit').click(function(){
	    	var lxcheck = $('.join-invite-email input[name="lxcheck"]').val();
	        if(!lxcheck){
	        	$.tips({content:'请输入手机号', stayTime:2000});
	        	return false;
	        }
	        if(!lxcheck.match(/1(\d{10})/) || lxcheck.length !== 11){
	        	$.tips({content:'请输入正确的手机号', stayTime:2000});
	        	return false;
	        }
	        var jcpche = $('.join-invite-email input[name="joinCapche"]').val();
	        if(!jcpche){
	        	$.tips({content:'请输入手机验证码', stayTime:2000});
	        	return false;
	        }
		    data = {uid:'<?php echo $uid?>', channel:'<?php echo $channel?>', tchl:2, lxcheck:$("input[name='lxcheck']").val(), joinCapche:$("input[name='joinCapche']").val()};
		    $.ajax({
                type: 'post',
                url:  '/activity/joinattend',
                data: data,
                dataType: 'json',
                success: function(response) {
                	if (response.status !== true) {
                    	if (response.status == 2) {
                    		$('#captcha_reg').attr('src', '/mainajax/captcha?v=' + Math.random());
    	                	closeTimer(1);
                    	}
	                	$.tips({
                            content:response.msg,
                            stayTime:2000
                        });
	                	$('input[name="joinCapche"]').val('');
	                 }else {
	                	 $(".join-invite").html('<div class="join-ticket"><strong>188元</strong><p><b>恭喜您获得红包</b>亿万大奖等你赢</p><a href="<?php echo $this->config->item('pages_url')?>/app/download?c=invite_activity" class="btn-download">下载APP使用<?php if ($platform === 'app') {?>（2.45M）<?php }?></a></div>')
	                 }
                }
            });
		})
		$(".change-img").on('click', function(){
			$('#captcha_reg').attr('src', '/mainajax/captcha?v=' + Math.random());
	        $('.join-invite-email input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
	        closeTimer(1);
	        return false;
	    });
    });

    </script>
</body>
</html>