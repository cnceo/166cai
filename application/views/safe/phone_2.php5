<div class="tit-b">
	<h2>绑定手机</h2>
	<p class="tip cOrange">为保障您的账户安全，请绑定您的真实手机</p>
</div>
<?php if(!empty($this->uinfo['phone'])):?>
<ul class="steps-bar clearfix">
	<li><i>1</i><span class="des">验证身份</span></li>
	<li><i>2</i><span class="des">手机绑定</span></li>
	<li class="last cur"><i>3</i><span class="des">绑定完成</span></li>
</ul>
<?php endif;?>
<div class="safe-item-box safe-success">
	<div class="sc-tip"><i class="icon icon-cYes"></i>恭喜您，手机号码绑定成功！</div>
	<p><a href="/safe">返回安全中心>></a></p>
</div>