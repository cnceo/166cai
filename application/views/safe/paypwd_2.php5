<div class="tit-b">
	<?php if($actiontype=='setPayPwd'):?>
	<h2>设置支付密码</h2>
	<?php else:?>
	<h2>修改支付密码</h2>
	<?php endif;?>
	<p class="tip cOrange">支付密码为购买彩票付款时输入，不等同于登录密码</p>
</div>
<div class="tab-content">
	<?php if($actiontype=='setPayPwd'):?>
	<ul class="steps-bar clearfix">
		<li><i>1</i><span class="des">设置支付密码</span></li>
		<li class=""><i>2</i><span class="des">验证身份</span></li>
		<li class="last cur"><i>3</i><span class="des">操作成功</span></li>
	</ul>
	<div class="tab-item pt20" style="display:block;">
		<div class="safe-item-box safe-success">
			<div class="sc-tip"><i class="icon icon-cYes"></i>恭喜您，支付密码设置成功！</div>
			<p><a href="/safe">返回安全中心&gt;&gt;</a></p>
		</div>
	</div>
	<?php elseif($actiontype=='resetPayPwd'):?>
	<ul class="steps-bar clearfix">
		<li><i>1</i><span class="des">验证身份</span></li>
		<li class=""><i>2</i><span class="des">设置支付密码</span></li>
		<li class="last cur"><i>3</i><span class="des">设置成功</span></li>
	</ul>
	<div class="tab-item pt20" style="display:block;">
		<div class="safe-item-box safe-success">
			<div class="sc-tip"><i class="icon icon-cYes"></i>恭喜您，新支付密码已生效！</div>
			<p><a href="/safe">返回安全中心&gt;&gt;</a></p>
		</div>
	</div>
	<?php endif;?>
</div>