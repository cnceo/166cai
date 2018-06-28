<!-- 意见反馈 begin -->
<div class="feedbackPopWrap pub-pop" style="display: none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2><i>*</i>您有什么想对我们说的吗？</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body feedback-submit">
			<ul class="feedback-tab">
				<li class="active">
					<label for="feedbackQ">
						<input type="radio" checked="checked" name="feedback" id="feedbackQ" value="1">问题
					</label>
				</li>
				<li>
					<label for="feedbackJ">
						<input type="radio" name="feedback" id="feedbackJ" value="2">建议
					</label>
				</li>
			</ul>
			<div class="feedback-tab-wrap">
				<div class="feedback-tab-cot active">
					<div class="textarea-box">
						<textarea class="feedback-box" name="" id="" rows="7" value="快来写下遇到的问题或者您对我们的建议，我们会参考您的留言优化产品"></textarea>
					</div>
				</div>
			</div>
            <p style="margin-top:10px;">紧急问题，立刻回复，试试<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=2584565084&amp;site=qq&amp;menu=yes">在线客服</a></p>
			<div class="btn-group">
				<a class="btn btn-confirm-large submit" href="javascript:;" target="_self">提交</a>
			</div>
		</div>
	</div>
</div>
<!-- 意见反馈 end -->
<!-- 温馨提示 begin -->
<div class="tipsPopWrap pub-pop" style="display: none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>温馨提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<h3 class="tipsPopWrap-title"><i class="icon-suc"></i>您的留言我们已收到，<br>感谢您的支持，祝您购彩愉快！</h3>
            <?php if($this->uid): ?>
			<p class="tipsPopWrap-aside">您可以到首页右上角<a href="/mynews?cpage=1">“我的消息”</a>中查看我们的回复信息。</p>
            <?php endif; ?>
			<p class="tipsPopWrap-close-time"><span><em id="tipsPopWrap-close-time">5</em>秒后自动关闭</span></p>
		</div>
	</div>
</div>
<!-- 温馨提示 end -->
<script>
	//意见反馈
	cx.feedbackPopWrap_pro = (function() {
        var me = {};
        var $wrapper = $('.feedbackPopWrap');

        $wrapper.find('.pop-close').click(function() {
            $wrapper.hide();
            cx.Mask.hide();
        });

        me.show = function() {
            $('.feedbackPopWrap').remove();
            $wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
        };

        me.hide = function() {
            $wrapper.hide();
            cx.Mask.hide();
        };

        return me;
    })();

	$('.feedBack').click(function(){
		cx.feedbackPopWrap_pro.show();
	})

	//温馨提示
	cx.tipsPopWrap_pro = (function() {
        var me = {};
        var $wrapper = $('.tipsPopWrap');

        $wrapper.find('.pop-close').click(function() {
            $wrapper.hide();
            cx.Mask.hide();
        });

        me.show = function() {
        	$('.feedbackPopWrap').hide();
            $wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
        };

        me.hide = function() {
            $wrapper.hide();
            cx.Mask.hide();
        };

        return me;
    })();

	new cx.vform('.feedback-submit', {
		renderTip: 'renderTips',
        submit: function(data) {
            var feedtype = $(".feedback-tab input[type='radio']:checked").val();
            var feedcontent = $(".feedback-box").val();
            $.ajax({
                type: 'post',
                url:  '/mynews/feedback',
                data: {feedtype:feedtype,feedcontent:feedcontent},
                dataType: 'json',
                success: function(response) {
                	if(response.status == '000'){
                        $(".feedback-box").val('');
                		cx.tipsPopWrap_pro.show();
                        settime();
                		//setTimeout("cx.tipsPopWrap_pro.hide()",3000);
                	}else{
                		cx.Alert({
                            content: response.msg
                        });
                	}
                }
            });
        }
    });

    //倒计时
    var countdown=5; 
    function settime(val) { 
        if (countdown == 0) { 
            countdown = 5; 
            $("#tipsPopWrap-close-time").html(countdown);
            cx.tipsPopWrap_pro.hide();
            return false;
        } else { 
            $("#tipsPopWrap-close-time").html(countdown);
            countdown--; 
        } 
        setTimeout(function() { 
            settime(val) 
        },1000) 
    }

</script>
