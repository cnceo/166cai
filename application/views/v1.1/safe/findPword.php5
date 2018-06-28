<div class="wrap_in l-concise l-concise-col">
	<div class="l-concise-hd">
		<div class="steps steps-3">
			<ol><li class="active"><span><i>1</i>验证账号</span></li><li><span><i>2</i>安全验证</span></li><li><span><i>3</i>重置密码</span></li></ol>
		</div>
	</div>
	<div class="l-concise-bd findPword">
		<div class="l-concise-main">
			<!-- 第一步 -->
			<div class="form form-findpwd">
				<div class="form-item">
					<label class="form-item-label">手机号：</label>
					<div class="form-item-con">
						<input class="form-item-ipt vcontent" type="text" autocomplete="off" name="findpwd" data-rule="findpwd" data-ajaxcheck='1' value="" />
						<div class="form-tip"><i class="icon-tip"></i><span class="form-tip-con tip findpwd">请输入您的手机号</span><s></s></div>
					</div>
				</div>
				<div class="form-item btn-group">
					<div class="form-item-con">
						<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
						<a class="btn btn-main submit" href="javascript:;">下一步</a>
					</div>
				</div>
			</div>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
</div>
<script type="text/javascript">
$(function() {
	new cx.vform('.findPword', {
		renderTip: 'renderTips',
		sValidate: 'submitValidate',
        submit: function(data) {
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/safe/findPword',
                dataType: 'json',
                data: data,
                success: function(response) {
	                if(response.status == '000'){
	                	$('.l-concise-col').html(response.data);
                    }else{
                    	recaptcha_reg();
                    	cx.Alert({content:response.msg});
                    }
                }
            });
        }
    });
});
</script>
