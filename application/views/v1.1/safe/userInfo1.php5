<!--header end-->
<div class="wrap_in l-concise l-concise-col">
<!-- 注册成功 start -->
	<div class="l-concise-bd register-resulte">
		<div class="l-concise-main">
			<div class="mod-result result-success">
	<div class="mod-result-bd">
		<i class="icon-result"></i>
		<div class="result-txt">
			<h2 class="result-txt-title"><em><?php echo $this->uname;?></em>，恭喜你实名认证成功</h2>
			<?php if($hasRedpack):?><p class="result-txt-tips">3元实名认证红包已放入账户！<a href="/mylottery/redpack">查看全部红包</a></p><?php endif;?>
		</div>
	</div>
	<div class="mod-result-ft">
		<?php if($hasRedpack):?>
		<a href="/ssq?fr-sm" class="btn-b btn-main">立即购彩</a>
		<?php else :?>
		<a href="/ssq?fr-sm" class="btn-b btn-main">立即购彩</a>
		<?php endif;?>
	</div>
</div>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
	<!-- 注册成功 end -->
</div>
