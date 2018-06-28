<div class="pub-pop safe-center bind-form" >
	<div class="pop-in">
		<div class="pop-head">
			<h2>完善信息</h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<div class="mod_user">	
                <form class="form uc-form-list">
                    <?php if( !$is_id_bind ): ?>
                    <fieldset>
                        <h3><span class="form-tip"><i class="icon-tip"></i>真实身份信息是您领奖提现的依据，请如实填写</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>真实姓名</label>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" name="real_name" data-rule="chinese">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con real_name tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>身份证号</label>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" name="id_card" data-encrypt='1' data-ajaxcheck='1' data-rule="identification">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con id_card tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php endif; ?>

                    <?php if( !$is_pay_pwd ): ?>
                    <fieldset>
                        <h3><span class="form-tip"><i class="icon-tip"></i>请设置支付密码，用于账户交易时输入</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>支付密码</label>
                            <div class="form-item-con">
                                <input type="password" class="form-item-ipt vcontent" value="" data-rule='password' data-ajaxcheck='1' name="pay_pwd">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con pay_pwd tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>重复一次</label>
                            <div class="form-item-con">
                                <input type="password" class="form-item-ipt vcontent" data-rule='same' data-with='pay_pwd' value="" name="conpay_pwd">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con conpay_pwd tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php endif; ?>

                    <div class="form-item btn-group">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" target="_self" href="javascript:;">提交</a>
                            <a class="btn btn-default cancel" target="_self" href="javascript:;">取消</a>
                        </div>
                    </div>
                </form>
	        </div>
		</div>
	</div>
</div>
<script type="text/javascript">
<!--
	$(function(){
    
		new cx.vform('.bind-form', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  '/safe/bind',
	                data: data,
	                success: function(response) {
                        if(response == 1){
                        	cx.PopCom.hide('.bind-form');
							cx.Alert({
								content: '绑定成功',
								confirmCb: function() {
									cx.PopCom.hide('.bind-form');
                                    $('.not-bind').removeClass('not-bind');
								}
							});
                        } else if (response == 2 ) {
                            self.renderTip('支付密码为空或两次输入不一致', $('.pay_pwd'));
                        } else if (response == 3 ) {
                            self.renderTip('身份证号为空或两次输入不一致', $('.id_card'));
                        } else if (response == 4 ) {
                            self.renderTip('验证码错误', $('.newphoneyzm'));
                        } else if (response == 5 ) {
                            self.renderTip('身份证已绑定', $('.id_card'));
                        } else if (response == 6 ) {
                            self.renderTip('手机已绑定', $('.phone'));
                        } else if(response == 7){
                            self.renderTip('身份证格式错误', $('.id_card'));
                        } else {
                        }
	                }
	            });
	        }
	    });


	})    
//-->
</script>
