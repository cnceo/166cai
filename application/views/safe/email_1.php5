<div class="tit-b">
	<h2>绑定邮箱</h2>
</div>
<ul class="steps-bar clearfix">
	<li><i>1</i><span class="des">绑定邮箱</span></li>
	<li class="cur"><i>2</i><span class="des">验证邮件</span></li>
	<li class="last"><i>3</i><span class="des">绑定完成</span></li>
</ul>
<div class="safe-item-box mail-text-area">
	<div class="yahei fz18 mb5">邮件已发送至<strong class="c333 mlr5"><?php echo $email; ?></strong></div>
	<p class="c333">请立即<a href="<?php echo $redirectUrl; ?>" class="underline mlr5" target="_blank">登录邮箱</a>查看邮件完成绑定</p>
	<dl class="safe-way">
		<dt>没有收到邮件？</dt>
		<dd>
			<p>1、确认邮件地址是否正确，若有误请<a href="/safe/email" class="underline mlr5">返回修改</a></p>
			<p>2、可能被有些邮箱误认为是垃圾邮件，检查是否在垃圾箱中<a href="<?php echo $redirectUrl; ?>" class="underline mlr5" target="_blank">登录邮箱</a></p>
			<p>3、超过10分钟仍未收到邮件，请<a href="/safe/email" class="underline mlr5">重新发送</a></p>
		</dd>
		<dd><a href="/safe">返回安全中心>></a></dd>
	</dl>
</div>