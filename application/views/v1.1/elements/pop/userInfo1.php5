<form class="form edit-form">
	<div class="form-item">
		<label class="form-item-label">真实姓名：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" type="text" autocomplete="off" name="real_name"  data-rule="chinese" value="<?php echo $real_name;?>" />
			<div class="form-tip hide">
				<i class="icon-tip"></i>
				<span class="form-tip-con tip real_name"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="form-item">
		<label class="form-item-label">身份证号：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" type="text" name="id_card" value="" data-encrypt='1' data-ajaxcheck='1' data-rule="identification" />
			<div class="form-tip hide">
				<i class="icon-tip"></i>
				<span class="form-tip-con tip id_card"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="form-item">
		<label class="form-item-label">确认身份证号：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" type="text" name="conid_card" data-encrypt='1' data-rule='same' data-with='id_card' value="" />
			<div class="form-tip hide">
				<i class="icon-tip"></i>
				<span class="form-tip-con tip conid_card"></span>
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
<script type="text/javascript">
$(function () {
	new cx.vform('.edit-form', {
		renderTip: 'renderTips',
		submit: function (data) {
            var self = this;
            var data = data || {};
            data.rfsh = <?php echo isset($rfsh) ? $rfsh : 0?>;
            $.ajax({
                type: 'post',
                url: '/pop/getUserInfo1',
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
                        }else if( response == '110' ){
                        	cx.PopAjax.login(1);
                        	$(".pop-w-max").remove();
                        	$('.btn-betting').addClass('not-login');
                        	$('.not-bind').addClass('not-bind');
                        }else {
                        	$('.not-bind').removeClass('not-bind');
                        	$('.pop-body').html(response);
                        }
                }
            });
        }
	});
});
</script>