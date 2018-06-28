
<!--header end-->
<div class="wrap_in l-concise l-concise-col">
<!-- 注册成功 start -->
	<div class="l-concise-bd register-resulte">
		<div class="l-concise-main">
			<div class="mod-result result-success">
	<div class="mod-result-bd">
		<i class="icon-result"></i>
		<div class="result-txt">
			<h2 class="result-txt-title"><em><?php echo $this->uname;?></em>，恭喜你加入166彩票</h2>
		</div>
	</div>
	<div class="mod-result-ft">
		<p class="note">完善个人信息、购彩更便捷、账户更安全</p>
		<a href="/safe/userInfo" class="btn-b btn-main">实名认证</a>
		<?php if ($hongbao) {?>
		<div class="note-side">
			<div class="btn-tips-t">离获得<strong class="main-color-s">166元红包</strong>只剩最后一步，快实名认证吧！<b></b><s></s></div>
		</div>
		<?php }else {?>
		<p class="note-side">想中500万吗！去<a href="/hall">购彩大厅</a>逛逛</p>
		<?php }?>
	</div>
</div>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
	<!-- 注册成功 end -->
</div>
