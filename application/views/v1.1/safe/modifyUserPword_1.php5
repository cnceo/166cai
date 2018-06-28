<?php $position = $this->config->item('POSITION')?>
<div class="l-concise-hd">
	<div class="steps steps-2">
		<ol>
			<li><span><i>1</i>安全验证</span></li>
			<li class="active"><span><i>2</i>重置密码</span></li>				
		</ol>
	</div>
</div>
<div class="l-concise-bd modifyUserPword">
	<div class="l-concise-main">
		<!-- 第一步 -->
		<div class="form form-modifyUserPword">
			<div class="form-item">
				<label class="form-item-label">手机号：</label>
				<div class="form-item-con">
					<span class="form-item-txt"><?php echo substr_replace($this->uinfo['phone'],'****',3,4);?></span>
				</div>
			</div>
			<div class="form-item">
				<label class="form-item-label">输入新密码：</label>
				<div class="form-item-con">
					<input type="password" value="" class="form-item-ipt vcontent" name="pword" data-rule="password" data-encrypt="1">
					<div class="form-tip hide">
						<i class="icon-tip"></i>
						<span class="form-tip-con tip pword">输入新密码</span>
						<s></s>
					</div>
				</div>
			</div>
			<div class="form-item">
				<label class="form-item-label">再次输入新密码：</label>
				<div class="form-item-con">
					<input type="password" value="" class="form-item-ipt vcontent" name="con_pword" data-rule="same" data-encrypt="1" data-with="pword">
					<div class="form-tip hide">
						<i class="icon-tip"></i>
						<span class="form-tip-con tip con_pword"></span>
						<s></s>
					</div>
				</div>
			</div>
			<div class="form-item btn-group">
				<div class="form-item-con">
					<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_2'>
					<a class="btn btn-main submit" href="javascript:;">下一步</a>
				</div>
			</div>
		</div>
	</div>
	<?php $this->load->view('v1.1/elements/common/appdownload');?>
</div>

<script type="text/javascript">
$(function() {
    // 下一步
    new cx.vform('.modifyUserPword', {
		renderTip: 'renderTips',
		sValidate: 'submitValidate',
        submit: function(data) {
        	data.captcha = '<?php echo (string)$modifyCaptcha?>';
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/safe/modifyUserPword',
                data: data,
                success: function(response) {
	                if(response.status == '300'){
	                	$('.l-concise-col').html(response.data);
                    }else{
                    	cx.Alert({content:response.msg});
                    }
                }
            });
        }
    });
});
</script>
