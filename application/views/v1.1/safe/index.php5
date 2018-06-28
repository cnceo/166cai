<?php 
	$this->load->view('v1.1/elements/user/menu');
	$phone_send = (isset($this->uinfo['msg_send']) && ($this->uinfo['msg_send'] & 1)) ? true : false;
	$email_send = (isset($this->uinfo['msg_send']) && ($this->uinfo['msg_send'] & 2)) ? true : false;
?>
        <div class="l-frame-cnt safe-center">
        <div class="uc-main">
            <div class="safe-hd">
				<h2 class="tit">安全中心</h2>
			</div>
			<ul class="safe-info">
				<?php if($this->uinfo['pword']):?>
				<li class="over">
					<div class="state"><i></i>已设置</div>
					<div class="title">登录密码</div>
					<div class="supply">保障您的账户信息及资金安全</div>
					<div class="operate">已设置，<a target="_blank" href="/safe/modifyUserPword">修改</a></div>
				</li>
				<?php endif;?>
				<?php if($this->uinfo['phone']):?>
				<li class="over">
					<div class="state"><i></i>已绑定</div>
					<div class="title">手机号码</div>
					<div class="supply">您验证的手机：<strong><?php echo substr_replace($this->uinfo['phone'],'****',3,4);?></strong></div>
					<div class="operate">已绑定，<a target="_blank" href="/safe/modifyUserPhone">修改</a></div>
				</li>
				<?php else:?>
				<li>
					<div class="state"><i></i>未绑定</div>
					<div class="title">手机号码</div>
					<div class="supply">验证后,可用于快速找回登录密码，接收账户余额变动提醒。</div>
					<div class="operate"></div>
				</li>
				<?php endif;?>
				<?php if($this->uinfo['id_card']):?>
				<li class="over">
					<div class="state"><i></i>已绑定</div>
					<div class="title">真实身份</div>
					<div class="supply">真实姓名：<strong><?php echo cutstr($this->uinfo['real_name'], 0, 1);?></strong>，身份证号码：<strong><?php echo cutstr($this->uinfo['id_card'], 0, 12);?></strong></div>
					<div class="operate">已绑定，<a href="javascript:;" class="seeCard">查看</a></div>
				</li>
				<?php else:?>
				<li>
					<div class="state"><i></i>未绑定</div>
					<div class="title">真实身份</div>
					<div class="supply">实名信息是领奖、提现时核对提现人身份的重要信息,绑定后不能修改。</div>
					<div class="operate"><a href="javascript:;" class="not-bind">绑定</a></div>
				</li>
				<?php endif;?>
				<?php if($this->uinfo['email']):?>
				<li class="over">
					<div class="state"><i></i>已绑定</div>
					<div class="title">邮箱</div>
					<div class="supply">您验证的邮箱：<strong><?php echo $this->uinfo['email'];?></strong></div>
					<div class="operate">已绑定，<a target="_blank" href="/safe/modifyEmail">修改</a></div>
				</li>
				<?php else:?>
				<li>
					<div class="state"><i></i>未绑定</div>
					<div class="title">邮箱</div>
					<div class="supply">绑定邮箱后，成功出票将发送方案至您绑定的邮箱中。</div>
					<div class="operate"><a href="/safe/bindEmail">绑定</a></div>
				</li>
				<?php endif;?>
				<!-- <li <?php if ($phone_send){?>class="over"<?php }?>>
					<div class="state"><i></i><?php echo $phone_send ? '已':'未';?>开启</div>
					<div class="title">出票提醒(短信)</div>
					<div class="supply">开启后，成功出票将会给您绑定的手机号发送短信通知</div>
					<div class="operate">已<?php echo $phone_send ? '开启':'关闭';?>，<a href="javascript:;" 
					class="msg-send" data-id="phone" data-modify="<?php echo !$phone_send ? '1' : '0'?>"><?php echo !$phone_send ? '开启':'关闭'?></a></div>
				</li> -->
				<?php if($this->uinfo['email']):?>
				<li <?php if ($email_send){?>class="over"<?php }?>>
					<div class="state"><i></i><?php echo $email_send ? '已':'未';?>开启</div>
					<div class="title">出票提醒(邮件)</div>
					<div class="supply">开启后，成功出票将会给您绑定的邮箱发送邮件通知（老11选5等高频彩除外）</div>
					<div class="operate">已<?php echo $email_send ? '开启':'关闭';?>，<a href="javascript:;" 
					class="msg-send" data-id="email" data-modify="<?php echo !$email_send ? '1' : '0'?>"><?php echo !$email_send ? '开启':'关闭'?></a></div>
				</li>
				<?php endif;?>
			</ul>
			<div class="warm-tip mt30">
				<h3>温馨提示：</h3>
				<p>
					<em class="cOrange">中奖很重要，账户安全更重要，建议您绑定所有安全信息，以保障账户及资金安全。</em>
				</p>
			</div>
			</div>
		</div> 
<?php $this->load->view('v1.1/elements/user/menu_tail');?>
<script type="text/javascript">
$(function() {
	$('.seeCard').on("click", function() {
		if (!($(this).hasClass('disabled'))) {
			$('.seeCard').addClass('disabled');
			$.ajax({
	            type: 'post',
	            url:  '/pop/getCardPop',
	            data: {'version':version},
	            success: function(response) {
	                $('body').append(response);
	                cx.PopCom.show('.pop-id');
	                cx.PopCom.close('.pop-id');
	                cx.PopCom.cancel('.pop-id');
	                $('.seeCard').removeClass('disabled');
	            }
	        });
		}
	});
	$('body').on('click', ".msg-send", function(){
		$_this = $(this);
		if (!($_this.hasClass('disabled'))) {
			$_this.addClass('disabled');
			var send = $_this.data('modify');
			var type = $_this.data('id');
			$.ajax({
	            type: 'post',
	            url:  '/api/user/updateMsgsend',
	            data: {uid:'<?php echo $this->uid?>', msg_send:send, type:type},
	            dataType : 'json',
	            success: function(response) {
		            switch(send) {
		            	case 1:
		            		$_this.parents('li').addClass('over');
		            		$_this.parents('li').find('div:first').html('<i></i>已开启');
		            		$_this.parents('li').find('div:last').html('已开启，<a href="javascript:;" class="msg-send" data-id="'+type+'" data-modify="0">关闭</a>');
		            		break;
		            	case 0:
		            		$_this.parents('li').removeClass('over');
		            		$_this.parents('li').find('div:first').html('<i></i>未开启');
		            		$_this.parents('li').find('div:last').html('已关闭，<a href="javascript:;" class="msg-send"  data-id="'+type+'" data-modify="1">开启</a>');
			            	break;
		            }
	            }
	        });
		}
	})
});
</script>