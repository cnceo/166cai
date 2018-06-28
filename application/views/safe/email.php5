<?php $this->load->view('elements/user/menu');?>
<div class="article">
	<div class="tit-b">
		<h2>修改邮箱</h2>
	</div>
	<?php if($email_tag): ?>
	<ul class="steps-bar clearfix">
		<li><i>1</i><span class="des">绑定邮箱</span></li>
		<li><i>2</i><span class="des">验证邮件</span></li>
		<li class="last cur"><i>3</i><span class="des">绑定完成</span></li>
	</ul>
	<div class="safe-item-box safe-success">
		<div class="sc-tip"><i class="icon icon-cYes"></i>恭喜您，邮箱绑定成功！</div>
		<p><a href="/safe">返回安全中心&gt;&gt;</a></p>
	</div>
	<?php else: ?>
	<ul class="steps-bar clearfix">
		<li class="cur"><i>1</i><span class="des">绑定邮箱</span></li>
		<li><i>2</i><span class="des">验证邮件</span></li>
		<li class="last"><i>3</i><span class="des">绑定完成</span></li>
	</ul>
	<div class="safe-item-box">
		<form class="form uc-form-list pl154">
			<?php if($this->uinfo['email']): ?>
			<input type='hidden' class='vcontent' name='action' value='_1'>
			<div class="form-item">
				<label class="form-item-label">原邮箱地址</label>
				<div class="form-item-con"><span class="form-item-txt"><?php echo $hide_email ;?></span></div>
			</div>
			<div class="form-item">
				<label class="form-item-label">原邮箱地址</label>
				<div class="form-item-con">
					<input type="text" class="form-item-ipt vcontent" data-rule='same_email' data-ajaxcheck='1' value="" name="validate_email">
					<div class="form-tip hide">
	            		<i class="icon-tip"></i>
		            	<span class="form-tip-con validate_email tip"></span>
		            	<s></s>
		            </div>
				</div>
			</div>
			<div class="form-item">
				<label class="form-item-label">新邮箱地址</label>
				<div class="form-item-con">
					<input type="text" class="form-item-ipt vcontent" data-rule='email' data-ajaxcheck='1' value="" name="email">
					<div class="form-tip hide">
	            		<i class="icon-tip"></i>
		            	<span class="form-tip-con email tip"></span>
		            	<s></s>
		            </div>
				</div>
			</div>
			<?php else: ?>
			<input type='hidden' class='vcontent' name='action' value='_2'>
			<div class="form-item">
				<label class="form-item-label">绑定邮箱地址</label>
				<div class="form-item-con">
					<input type="text" class="form-item-ipt vcontent" data-rule='email' data-ajaxcheck='1' value="" name="email">
					<div class="form-tip hide">
	            		<i class="icon-tip"></i>
		            	<span class="form-tip-con email tip"></span>
		            	<s></s>
		            </div>
				</div>
			</div>
		    <?php endif; ?>
			<div class="form-item btn-group">
				<div class="form-item-con">
					<a href="javascript:;" class="btn btn-confirm submit">下一步</a>
				</div>
			</div>
		</form>
	</div>
	<div class="warm-tip mt30">
		<h3>温馨提示：</h3>
		<p>1.用于找回密码的重要联系方式，为保障账户安全，请您仔细核对填写；</p>
		<p>2.第一时间收到2345重大优惠活动的邮件。</p>
	</div>
    <script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
	<script type="text/javascript">
		$(function(){
			new cx.vform('.safe-item-box', {
				renderTip: 'renderTips',
		        submit: function(data) {
		            var self = this;
		            $.ajax({
		                type: 'post',
		                url:  '/safe/email',
		                data: data,
		                success: function(response) {
		                	if(response == 2){
		                		cx.Alert({
                                    content: '邮箱地址验证错误！'
                                });
		                	}else if(response == 3){
		                		cx.Alert({
                                    content: '原邮箱地址验证错误！'
                                });
		                	}else if(response == 4){
		                		cx.Alert({
                                    content: '此邮箱地址已绑定过！'
                                });
		                	}else{
		                		$('.article').html(response);
		                	}
		                }
		            });
		        }
		    });
		})
	</script>
	<?php endif; ?>
</div> 
<?php $this->load->view('elements/user/menu_tail');?>