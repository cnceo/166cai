<div class="pub-pop safe-center bind-bank-form" >
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
                            <div class="form-item-con"><input type="text" class="ipt_text vcontent" value="" name="real_name" data-rule="chinese"></div>
                            <div class="form_tips" style='display:none;'>
                                <i class="tips_icon"></i>
                                <div class="tips_con real_name tip"></div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>身份证号</label>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" name="id_card" data-ajaxcheck='1' data-encrypt='1' data-rule="identification">
                                <div class="form-tip" style="display:none;">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con id_card tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php endif; ?>

                    <?php if( !$is_phone_bind ): ?>
                    <fieldset>
                    <div class="form-item">
                        <label class="form-item-label"><b>*</b>手机号码</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" data-rule='phonenum' data-ajaxcheck='1' data-freeze='_timer' name="phone">
                            <div class="form-tip" style="display:none;">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con phone tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-vcode">
                        <label class="form-item-label"><b>*</b>验证码</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vyzm vcontent" data-rule='checkcode' value="" name="newphoneyzm">
                            <a class="lnk-getvcode _timer" data-freeze='phone' id='btn-getYzm' target="_self" href="javascript:;">获取语音验证码</a>
                            <span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
                            <div class="form-tip" style="display:none;">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con newphoneyzm tip"></span>
                                <s></s>
                            </div>
                            <div style="left: 0; top: 46px;" class="ui-poptip ui-poptip-yuyin">
						      <div class="ui-poptip-container">
						        <div class="ui-poptip-arrow-top"><i>◆</i><span>◆</span></div>
						        系统将拨打您的手机语音播报验证码，请注意接听。<a target="_blank" href="/help/index/b0-f4">未收到验证码？</a>
						      </div>
						    </div>
                        </div>
                    </div>
                    </fieldset>
                    <?php endif; ?>

                    <?php if( !$is_pay_pwd ): ?>
                    <fieldset>
                        <h3><span class="form-tip"><i class="icon-tip"></i>请设置支付密码，用于账户交易时输入</span></h3>
                    <div class="form-item">
                        <label class="form-item-label">新支付密码</label>
                        <div class="form-item-con">
                            <input type="password" class="form-item-ipt vcontent" value="" data-ajaxcheck='1' data-rule='password' name="pay_pwd">
                            <div class="form-tip form-tip-ok"  style="display:none;">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con pay_pwd tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label">重复支付密码</label>
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

                    <?php if( !$is_bank_bind ): ?>
                    <fieldset>
                    <div class="form-item">
                        <label class="form-item-label">开户银行</label>
                        <div class="form-item-con">
                            <dl class="simu-select-med">
                                <dt><span class='_scontent' id='province' data-value=''>请选择</span><i class="arrow"></i><input type="hidden" class="vcontent" name='bank_type' value=''></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in" data-name='bank_name'>
                                        <?php foreach($bankTypeList as $key => $val): ?>
                                        <a href="javascript:;" target="_self" data-value='<?php echo $key; ?>'><?php echo $val;?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </dd>
                            </dl>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con bank_name tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label">银行卡号</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" data-rule="bankcard" name="bank_id" >
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con bank_id tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-add">
                        <label class="form-item-label">开户地区</label>
                        <div class="form-item-con">
                            <input type='hidden' class='vcontent' name='action' value='_1'>
                            <dl class="simu-select-med" data-target='city_list'>
                                <dt>
                                    <span class='_scontent' id='province' data-value=''>请选择</span><i class="arrow"></i>
                                    <input type="hidden" class="vcontent" name='province' value=''>
                                </dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in" data-name='province'>
                                        <?php foreach($provinceList as $row): ?>
                                        <a href="javascript:;" target="_self" data-value='<?php echo $row['province']?>'><?php echo $row['province']?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </dd>
                            </dl>
                            <dl class="simu-select-med city_list">
                                <dt>
                                    <span class='_scontent' id='city' data-value=''>请选择</span><i class="arrow"></i>
                                    <input type="hidden" class="vcontent" name='city' value=''>
                                </dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in" id='city-container' data-name='city'>
                                    </div>
                                </dd>
                            </dl>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con bank_area tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    </fieldset>
                    <?php endif; ?>
                    <div class="form-item btn-group">
                        <a class="btn btn-confirm submit" target="_self" href="javascript:;">提交</a>
                        <a class="btn btn-default cancel" target="_self" href="javascript:;">取消</a>
                    </div>
                </form>
	        </div>
		</div>
	</div>
</div>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/vform.min.js');?>"></script>
<script type="text/javascript">
<!--
	$(function(){

		new cx.vform('.bind-bank-form', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  '/safe/bindBank',
	                data: data,
	                success: function(response) {
                        if( response == 2 ){
                            self.renderTip('请选择开户地区', $('.bank_area'));
                        }else if (response == 3 ) {
                            self.renderTip('请选择开户银行', $('.bank_name'));
                        }else if (response == 4 ) {
                            self.renderTip('请输入正确的银行卡号', $('.bank_id'));
                        }else if (response == 1) {
                            cx.PopBankBind.hide();
                            cx.Alert({
                                content: '绑定成功',
                                confirmCb: function() {
                                    $('.not-bind-bank').off('click', showBankBind);
                                    $('.not-bind-bank').removeClass('not-bind-bank');
                                }
                            });
                        } else {
                        }
	                }
	            });
	        }
	    });
	})    
//-->
</script>
