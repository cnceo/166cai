<div class="wrap_in l-concise l-concise-col">
	<div class="l-concise-bd">
		<div class="l-concise-main">
			<form class="form form-user-info">
        <div class="form-tips-bar"><i></i>真实身份信息是您领奖提现的依据且不可更改，请如实填写</div>
		<div class="form-item">
			<label class="form-item-label">真实姓名：</label>
			<div class="form-item-con">
				<input class="form-item-ipt vcontent" type="text" autocomplete="off" name="real_name" value=""  data-rule="chinese" />
				<div class="form-tip">
					<i class="icon-tip"></i>
					<span class="form-tip-con real_name tip">请输入正确的中文名</span>
					<s></s>
				</div>
			</div>
		</div>
		<div class="form-item">
			<label class="form-item-label">身份证号：</label>
			<div class="form-item-con">
				<input class="form-item-ipt vcontent" type="text" name="id_card" value="" data-encrypt='1' data-ajaxcheck='1' data-rule="identification" />
				<div class="form-tip">
					<i class="icon-tip"></i>
					<span class="form-tip-con id_card tip">请输入真实的身份证号</span>
					<s></s>
				</div>
			</div>
		</div>
		<div class="form-item">
			<label class="form-item-label">确认身份证号：</label>
			<div class="form-item-con">
				<input class="form-item-ipt vcontent" type="text" name="conid_card" data-encrypt='1' data-rule='same' data-with='id_card' value="" />
				<div class="form-tip">
					<i class="icon-tip"></i>
					<span class="form-tip-con conid_card tip">请再次输入身份证号</span>
					<s></s>
				</div>
			</div>
		</div>
		<div class="form-item btn-group">
			<div class="form-item-con">
				<a class="btn btn-main submit" href="javascript:;">下一步</a>
			</div>
		</div>
	</form>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
	<div class="mod-note">
		<h3 class="mod-note-title">温馨提示：</h3>
		<ol class="mod-note-list">
			<li>1、真实姓名是您提现时的重要依据，填写后不可更改（请保证身份证姓名与银行卡姓名保持一致，否则无法提现）。</li>
			<li>2、网站不向未满18周岁的青少年出售彩票。</li>
			<li>3、您的个人信息将被严格保密，不会用于任何第三方用途。</li>
		</ol>
	</div>
</div>
<script type="text/javascript">
$(function () {
	new cx.vform('.form-user-info', {
		renderTip: 'renderTips',
		submit: function (data) {
            var self = this;
            var data = data || {};
            $.ajax({
                type: 'post',
                url: '/safe/userInfo',
                data: data,
                success: function (response) {
                        if( response == 1 ){
                        	self.renderTip('请输入真实姓名', $('.real_name'));
                        }else if( response == 2 ){
                        	self.renderTip('同一个身份证最多绑5个账号', $('.id_card'));
                        }else if( response == 3 ){
                        	self.renderTip('身份证格式错误', $('.id_card'));
                        }else if( response == 4 ){
                        	self.renderTip('身份证未满18周岁', $('.id_card'));
                        }else if( response == 5 ){
                        	cx.Alert({content:'操作失败'});
                        }else {
                            location = location.href;
                        }
                }
            });
        }
	});
});
</script>