<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/pay.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/vform.js?v=<?php echo VER_VFORM_JS; ?>"></script>
<script type="text/javascript">
$(function(){
	//组件表单交互
	$(".addCardForm p input").each(function(){
		var _this = $(this);
		$(this).focus(function(){
			_this.removeClass().addClass("hover");
			_this.next("span").hide();
		});
		$(this).blur(function(){
			if(_this.val() == ""){
				_this.removeClass();
				_this.next().show();
			} 
		});
	});
	$(".addCardForm p span").each(function(){
		var _this = $(this);
		$(this).click(function(){
			_this.prev("input").focus();
		});
	});
	var bindForm = new cx.vform('.bind-form', {
		submit: function(data) {
			var self = this;
			data['isJson'] = 1;
			data['isToken'] = 1;
			cx.ajax.post({
				url: cx.url.getPayUrl('payBinding/alipay/bind'),
				data: data,
				success: function(response) {
					if (response.code == 0) {
						location.href = baseUrl + 'account/withdraw';
					} else {
						self.renderTip(response.msg);
					}
				}
			});
		}
	})
});
</script>
<!--容器-->
<div class="addCardWrap clearfix">
	<h1>绑定支付宝</h1>
    <div class="addCardForm bind-form">
        <p>
        	<input type="text" data-rule="required" data-tip="账号不能为空" class="vcontent" name="id" />
	        <span>请填写支付宝账号</span>
        </p>
        <p>
        	<input type="text" data-rule="chinese" class="vcontent" name="name" />
	        <span>请填写支付宝姓名</span>
        </p>
        <p>
        	<em class="addCardError tip" style="display: none;"></em>
        </p>
        <p>
        	<strong>请准确填写以上信息，确保您的资金安全！</strong>
        </p>
        <p>
        	<a class="submit">确认</a>
        </p>
    </div>
</div>
<!--容器end-->
